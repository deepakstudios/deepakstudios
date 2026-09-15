<?php

/**
 * deploy.php
 *
 * GitHub webhook deployment endpoint.
 *
 * Triggered by a GitHub "push" webhook on the `main` branch.
 * Secured by HMAC-SHA256 signature verification.
 *
 * Two deployment methods (set config.deploy.method):
 *   'git'     - uses the git binary + shell (VPS/dedicated, shell access required).
 *   'archive' - pure PHP, downloads the GitHub zip archive and swaps the files
 *               over the web root. Works on shared hosting (cPanel) where
 *               shell / git are unavailable. config.php and storage/ are
 *               ALWAYS preserved.
 *
 * Safety guarantees (both modes):
 *  - Rejects invalid signatures (constant-time compare).
 *  - Rejects non-push events and non-main branches.
 *  - Deploys ONLY origin/main - never arbitrary commits/branches from payloads.
 *  - Acquires a deployment lock (single deployment at a time, auto-expiring).
 *  - Never interpolates untrusted webhook data into shell commands.
 *  - Runs migrations, then validates health.
 *  - Logs deployment results (no secrets) to file and DB.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require_once __DIR__ . '/config.php';
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/lib_migrations.php';

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
    $secret   = (string) ($config['webhook_secret'] ?? '');
    $computed = 'sha256=' . hash_hmac('sha256', $payload, $secret);

    return hash_equals($computed, (string) $signature);
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
        $age = time() - (int) @filemtime($lockFile);
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

/**
 * Download a URL to a local file using cURL or streams (whichever is available).
 */
function downloadFile(string $url, string $dest, bool $verifySsl = true): void
{
    if (function_exists('curl_init')) {
        $fp = @fopen($dest, 'w');
        if ($fp === false) {
            throw new RuntimeException("Cannot open destination for download: {$dest}");
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_FILE            => $fp,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 120,
            CURLOPT_SSL_VERIFYPEER  => $verifySsl,
            CURLOPT_SSL_VERIFYHOST  => $verifySsl ? 2 : 0,
            CURLOPT_USERAGENT       => 'Deepak-Studios-Deploy/1.0',
        ]);
        $ok = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        fclose($fp);
        if ($ok === false) {
            @unlink($dest);
            throw new RuntimeException('Download failed (cURL): ' . $err);
        }
        return;
    }

    if (ini_get('allow_url_fopen')) {
        $ssl = $verifySsl
            ? ['verify_peer' => true, 'verify_peer_name' => true]
            : ['verify_peer' => false, 'verify_peer_name' => false];
        $ctx = stream_context_create([
            'http'  => ['timeout' => 300, 'user_agent' => 'Deepak-Studios-Deploy/1.0'],
            'ssl'   => $ssl,
        ]);
        $data = @file_get_contents($url, false, $ctx);
        if ($data === false) {
            $err = error_get_last();
            throw new RuntimeException('Download failed (streams): ' . ($err['message'] ?? 'unknown'));
        }
        if (file_put_contents($dest, $data) === false) {
            throw new RuntimeException("Cannot write download to {$dest}");
        }
        return;
    }

    throw new RuntimeException('No download method available: enable cURL or allow_url_fopen.');
}

/**
 * Recursively copy a directory tree, preserving files that must not change.
 */
function syncFromArchive(string $srcDir, array $config): array
{
    $repoPath = $config['deploy']['repo_path'] ?? __DIR__;
    $skip     = array_fill_keys(['config.php', 'storage'], true);

    $copied = 0;
    $ri = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($ri as $item) {
        $rel = substr($item->getPathname(), strlen($srcDir) + 1);
        $rel = str_replace('\\', '/', $rel);

        $first = explode('/', $rel)[0];
        if (isset($skip[$first])) {
            continue;
        }

        $destPath = $repoPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);

        if ($item->isDir()) {
            if (!is_dir($destPath)) {
                mkdir($destPath, 0755, true);
            }
            continue;
        }

        if (!is_dir(dirname($destPath))) {
            mkdir(dirname($destPath), 0755, true);
        }
        if (copy($item->getPathname(), $destPath)) {
            $copied++;
        }
    }

    return ['copied' => $copied];
}

// ------------------------------------------------------------------------
// Request handling
// ------------------------------------------------------------------------

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    respond(405, 'method_not_allowed');
}

$rawBody = (string) file_get_contents('php://input');
if ($rawBody === '') {
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
$ref            = (string) ($payload['ref'] ?? '');
$branch         = preg_replace('#^refs/heads/#', '', $ref);
$commit         = (string) ($payload['after'] ?? '');
$allowedBranch  = (string) ($config['deploy']['branch'] ?? 'main');

if ($branch !== $allowedBranch || $commit === '' || $commit === '0000000000000000000000000000000000000000') {
    respond(200, 'ignored_branch', ['branch' => $branch]);
}

// --- Deployment starts here ---
$startedAt       = date('Y-m-d H:i:s');
$repoPath        = $config['deploy']['repo_path'] ?? __DIR__;
$logFile         = $config['deploy']['log_file'] ?? $repoPath . '/storage/logs/deployment.log';
$method          = (string) ($config['deploy']['method'] ?? 'git');
$prevVersion     = trim((string) @file_get_contents($repoPath . '/Version.txt')) ?: 'unknown';
$prevCommit      = 'unknown';
$newCommit       = 'unknown';
$repoOwner       = (string) ($config['deploy']['owner'] ?? 'deepakstudios');
$repoName        = (string) ($config['deploy']['repo'] ?? 'deepakstudios');
$deployOk        = false;
$errorMsg        = null;
$cwdBefore       = null;

logLine($logFile, "DEPLOY START method={$method} branch={$branch} prev_version={$prevVersion}");

if (!acquireLock($config)) {
    logLine($logFile, "DEPLOY BLOCKED: another deployment is running");
    respond(429, 'deployment_in_progress');
}

try {
    if ($method === 'archive') {
        // --- Pure-PHP deploy: download GitHub archive and swap files. ---
        $zipUrl = sprintf(
            'https://codeload.github.com/%s/%s/zip/refs/heads/%s',
            rawurlencode($repoOwner),
            rawurlencode($repoName),
            rawurlencode($allowedBranch)
        );

        $storageDir = $repoPath . '/storage';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $tmpZip = $storageDir . '/update-' . bin2hex(random_bytes(4)) . '.zip';
        $tmpDir = $storageDir . '/release-' . bin2hex(random_bytes(4));
        $verifySsl = (bool) ($config['deploy']['verify_ssl'] ?? true);

        try {
            downloadFile($zipUrl, $tmpZip, $verifySsl);

            $zip = new ZipArchive();
            if ($zip->open($tmpZip) !== true) {
                throw new RuntimeException('Cannot open downloaded archive (ZipArchive unavailable?)');
            }
            $zip->extractTo($tmpDir);
            $zip->close();

            // The archive contains a single root folder like "owner-repo-main/".
            $rootDir = null;
            $entries = glob($tmpDir . '/*');
            foreach ($entries as $entry) {
                if (is_dir($entry)) {
                    $rootDir = $entry;
                    break;
                }
            }
            if ($rootDir === null) {
                throw new RuntimeException('No release root found inside the archive.');
            }

            syncFromArchive($rootDir, $config);
        } finally {
            @unlink($tmpZip);
            if (is_dir($tmpDir)) {
                removeTree($tmpDir);
            }
        }

        $newVersion = trim((string) @file_get_contents($repoPath . '/Version.txt')) ?: 'unknown';

    } else {
        // --- Git deploy: fetch + reset origin/main. ---
        [$code, $out] = safeExec(
            'cd ' . escapeshellarg($repoPath) . ' && git fetch origin ' . escapeshellarg($allowedBranch)
        );
        if ($code !== 0) {
            throw new RuntimeException('git fetch failed: ' . implode(' | ', $out));
        }

        [$code, $out] = safeExec('cd ' . escapeshellarg($repoPath) . ' && git rev-parse HEAD');
        if ($code === 0) {
            $prevCommit = trim($out[0] ?? '');
        }

        [$code, $out] = safeExec(
            'cd ' . escapeshellarg($repoPath) . ' && git reset --hard origin/' . escapeshellarg($allowedBranch)
        );
        if ($code !== 0) {
            throw new RuntimeException('git reset failed: ' . implode(' | ', $out));
        }

        [$code, $out] = safeExec('cd ' . escapeshellarg($repoPath) . ' && git rev-parse HEAD');
        if ($code === 0) {
            $newCommit = trim($out[0] ?? '');
        }

        $newVersion = trim((string) @file_get_contents($repoPath . '/Version.txt')) ?: 'unknown';
    }

    // --- Migrations (in-process, avoids shell dependency on shared hosts). ---
    ensureStorageDirectories($config);
    $pdo            = connectDb($config);
    $migrationLog   = runMigrations($pdo, getMigrations());
    $migrationResult = empty($migrationLog['applied']) ? 'no_new_migrations' : 'ok';

    // --- Health validation (in-process DB check; no shell needed). ---
    $pdo->query('SELECT 1');

    $deployOk = true;

} catch (Throwable $e) {
    $errorMsg = $e->getMessage();
} finally {
    $newVersion    = trim((string) @file_get_contents($repoPath . '/Version.txt')) ?: ($newVersion ?? 'unknown');
    $finishedAt    = date('Y-m-d H:i:s');
    $status        = $deployOk ? 'success' : 'failure';

    logLine($logFile, sprintf(
        "DEPLOY END status=%s method=%s prev=%s new=%s migration_result=%s%s",
        $status,
        $method,
        $prevVersion,
        $newVersion,
        $migrationResult ?? 'n/a',
        $errorMsg !== null ? ' error=' . $errorMsg : ''
    ));

    // Record into the database (best-effort; never fail on a DB write error).
    try {
        $db = $config['database'];
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $db['host'], $db['port'] ?? '3306', $db['database'], $db['charset'] ?? 'utf8mb4'
        );
        $pdoLog = new PDO($dsn, $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $stmt = $pdoLog->prepare(
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
        logLine($logFile, 'DB log insert skipped (non-fatal): ' . $dbErr->getMessage());
    }

    releaseLock();
}

if (!$deployOk) {
    respond(500, 'deployment_failed', ['error' => $errorMsg]);
}

respond(200, 'ok', [
    'method'           => $method,
    'previous_version' => $prevVersion,
    'new_version'      => $newVersion,
    'migrated'         => $migrationLog['applied'] ?? [],
]);

/**
 * Remove a directory tree recursively (used to clean up temp releases).
 * Defined at the bottom so it does not clash with the main flow.
 */
function removeTree(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $item) {
        if ($item->isDir()) {
            @rmdir($item->getPathname());
        } else {
            @unlink($item->getPathname());
        }
    }
    @rmdir($dir);
}