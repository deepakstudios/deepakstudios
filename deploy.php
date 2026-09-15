<?php

/**
 * deploy.php
 *
 * GitHub webhook deployment endpoint.
 *
 * Triggered by a GitHub "push" webhook on the `main` branch.
 * Secured by HMAC-SHA256 signature verification.
 *
 * Safety guarantees:
 *  - Rejects invalid signatures (constant-time compare).
 *  - Rejects non-push events and non-main branches.
 *  - Acquires a deployment lock (single deployment at a time, auto-expiring).
 *  - Deploys ONLY origin/main.
 *  - Never interpolates untrusted webhook data into shell commands.
 *  - Runs migrations, then validates health.
 *  - Logs deployment results (no secrets) to DB and file.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require_once __DIR__ . '/config.php';
$config = require __DIR__ . '/config.php';

date_default_timezone_set('UTC');

header('Content-Type: application/json');

function respond(int $status, string $message, array $extra = []): void
{
    http_response_code($status);
    echo json_encode(array_merge(['status' => $message], $extra));
    exit;
}

function logLine(string $filename, string $line): void
{
    $dir = dirname($filename);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    @file_put_contents($filename, date('c') . "  " . $line . PHP_EOL, FILE_APPEND);
}

/**
 * Constant-time HMAC signature comparison.
 */
function verifySignature(array $config, string $signature, string $payload): bool
{
    $secret    = (string) ($config['webhook_secret'] ?? '');
    $computed  = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    $expected  = (string) $signature;

    return hash_equals($computed, $expected);
}

/**
 * Acquire an exclusive deployment lock.
 */
function acquireLock(array $config): bool
{
    $lockFile = $config['deploy']['lock_file'] ?? __DIR__ . '/storage/deploy.lock';
    $ttl      = (int) ($config['deploy']['lock_ttl'] ?? 600);

    if (!is_dir(dirname($lockFile))) {
        @mkdir(dirname($lockFile), 0755, true);
    }

    if (file_exists($lockFile)) {
        $age = time() - (int) filemtime($lockFile);
        if ($age < $ttl) {
            return false; // active lock
        }
        @unlink($lockFile); // stale lock — clear it
    }

    $fh = @fopen($lockFile, 'c');
    if ($fh === false) {
        return false;
    }
    if (!flock($fh, LOCK_EX | LOCK_NB)) {
        fclose($fh);
        return false;
    }
    ftruncate($fh, 0);
    fwrite($fh, (string) time());
    fflush($fh);

    // Keep the handle open for the duration of the deployment (released naturally on exit).
    $GLOBALS['__deploy_lock_handle'] = $fh;
    $GLOBALS['__deploy_lock_file']   = $lockFile;

    return true;
}

/**
 * Release the deployment lock.
 */
function releaseLock(): void
{
    if (isset($GLOBALS['__deploy_lock_handle'])) {
        $fh = $GLOBALS['__deploy_lock_handle'];
        flock($fh, LOCK_UN);
        fclose($fh);
        $GLOBALS['__deploy_lock_handle'] = null;
    }
    @unlink($GLOBALS['__deploy_lock_file'] ?? __DIR__ . '/storage/deploy.lock');
}

/**
 * Run a shell command safely. Untrusted values are never interpolated.
 */
function safeExec(string $command): array
{
    $output = [];
    $code   = 0;
    exec($command . ' 2>&1', $output, $code);
    return [$code, $output];
}

// ------------------------------------------------------------------------
// Request handling
// ------------------------------------------------------------------------

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    respond(405, 'method_not_allowed');
}

$rawBody = (string) file_get_contents('php://input');
if ($rawBody === '' || $rawBody === false) {
    respond(400, 'empty_payload');
}

$signature = (string) ($_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '');
if ($signature === '' || !verifySignature($config, $signature, $rawBody)) {
    respond(401, 'invalid_signature');
}

$event = (string) ($_SERVER['HTTP_X_GITHUB_EVENT'] ?? '');
if ($event !== 'push') {
    respond(200, 'ignored_event', ['event' => $event]);
}

try {
    $payload = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    respond(400, 'invalid_json');
}

// Validate repository + branch BEFORE doing any work.
$repoName = $payload['repository']['full_name'] ?? '';
$ref      = (string) ($payload['ref'] ?? '');
$branch   = preg_replace('#^refs/heads/#', '', $ref);
$commit   = (string) ($payload['after'] ?? '');

$allowedBranch = (string) ($config['deploy']['branch'] ?? 'main');

if ($branch !== $allowedBranch || $commit === '' || $commit === '0000000000000000000000000000000000000000') {
    respond(200, 'ignored_branch', ['branch' => $branch]);
}

// --- Deployment starts here ---
$startedAt     = date('Y-m-d H:i:s');
$logFile       = $config['deploy']['log_file'] ?? __DIR__ . '/storage/logs/deployment.log';
$repoPath      = $config['deploy']['repo_path'] ?? __DIR__;
$lockFile      = $config['deploy']['lock_file'] ?? __DIR__ . '/storage/deploy.lock';
$prevVersion   = trim((string) @file_get_contents($repoPath . '/Version.txt')) ?: 'unknown';
$prevCommit    = '';
$newCommit     = '';
$deployOk      = false;
$errorMsg      = null;
$migrationLog  = [];

logLine($logFile, "DEPLOY START (repo={$repoName}, branch={$branch}, prev_version={$prevVersion})");

if (!acquireLock($config)) {
    logLine($logFile, "DEPLOY BLOCKED: another deployment is running");
    respond(429, 'deployment_in_progress');
}

try {
    // 1. Fetch latest main.
    [$code, $out] = safeExec(
        'cd ' . escapeshellarg($repoPath) . ' && git fetch origin ' . escapeshellarg($branch)
    );
    if ($code !== 0) {
        throw new RuntimeException('git fetch failed: ' . implode(' | ', $out));
    }

    // 2. Determine previous commit, then reset working tree to origin/main.
    [$code, $out] = safeExec(
        'cd ' . escapeshellarg($repoPath) . ' && git rev-parse HEAD'
    );
    if ($code === 0) {
        $prevCommit = trim($out[0] ?? '');
    }

    [$code, $out] = safeExec(
        'cd ' . escapeshellarg($repoPath) . ' && git reset --hard origin/' . escapeshellarg($branch)
    );
    if ($code !== 0) {
        throw new RuntimeException('git reset failed: ' . implode(' | ', $out));
    }

    [$code, $out] = safeExec(
        'cd ' . escapeshellarg($repoPath) . ' && git rev-parse HEAD'
    );
    if ($code === 0) {
        $newCommit = trim($out[0] ?? '');
    }

    // 3. Run migrations (Install.php).
    [$code, $out] = safeExec(
        'php ' . escapeshellarg($repoPath . '/Install.php')
    );
    $migrationLog = $out;
    $migrationResult = ($code === 0) ? 'ok' : 'failed';
    if ($code !== 0) {
        throw new RuntimeException('Migrations failed: ' . implode(' | ', $out));
    }

    // 4. Health validation.
    $healthScript = $repoPath . '/health.php';
    [$code, $out] = safeExec('php ' . escapeshellarg($healthScript));
    if ($code !== 0) {
        throw new RuntimeException('Health check failed: ' . implode(' | ', $out));
    }

    $deployOk = true;

} catch (Throwable $e) {
    $errorMsg = $e->getMessage();
} finally {
    $newVersion   = trim((string) @file_get_contents($repoPath . '/Version.txt')) ?: 'unknown';
    $finishedAt   = date('Y-m-d H:i:s');
    $status       = $deployOk ? 'success' : 'failure';

    logLine($logFile, sprintf(
        "DEPLOY END status=%s prev=%s new=%s prev_commit=%s new_commit=%s migrations=%s%s",
        $status,
        $prevVersion,
        $newVersion,
        $prevCommit,
        $newCommit,
        $migrationResult ?? 'n/a',
        $errorMsg !== null ? ' error=' . $errorMsg : ''
    ));

    // Record into the database (best-effort; never fail on a DB write error).
    try {
        $pdo = (function () use ($config) {
            $db = $config['database'];
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $db['host'], $db['port'] ?? '3306', $db['database'], $db['charset'] ?? 'utf8mb4'
            );
            return new PDO($dsn, $db['username'], $db['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        })();

        $stmt = $pdo->prepare(
            "INSERT INTO deployment_logs
                (started_at, finished_at, previous_version, new_version,
                 previous_commit, new_commit, migration_result, status, error_message)
             VALUES
                (:started, :finished, :prev_v, :new_v, :prev_c, :new_c, :mig, :status, :err)"
        );
        $stmt->execute([
            ':started'  => $startedAt,
            ':finished' => $finishedAt,
            ':prev_v'   => $prevVersion,
            ':new_v'    => $newVersion,
            ':prev_c'   => $prevCommit,
            ':new_c'    => $newCommit,
            ':mig'      => $migrationResult ?? 'n/a',
            ':status'   => $status,
            ':err'      => $errorMsg,
        ]);
    } catch (Throwable $dbErr) {
        logLine($logFile, 'DB log insert failed (non-fatal): ' . $dbErr->getMessage());
    }

    releaseLock();
}

if (!$deployOk) {
    respond(500, 'deployment_failed', ['error' => $errorMsg]);
}

respond(200, 'ok', [
    'previous_version' => $prevVersion,
    'new_version'      => $newVersion,
    'previous_commit'  => $prevCommit,
    'new_commit'       => $newCommit,
    'migrations'       => $migrationLog,
]);