<?php

/**
 * health.php
 *
 * Health-check endpoint. Verifies the application can start and,
 * when configured, that the database connection works.
 *
 * Usage:
 *   CLI:     php health.php          (exit code 0 = healthy, 1 = unhealthy)
 *   Web:     GET /health.php         (returns JSON)
 *
 * The deployment system calls this after every deployment.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require_once __DIR__ . '/config.php';
$config = require __DIR__ . '/config.php';

date_default_timezone_set('UTC');

$checks   = [];
$healthy  = true;
$isCli    = (PHP_SAPI === 'cli');
$asJson   = !$isCli;

// 1. Application bootstrap check.
$checks[] = ['name' => 'application', 'status' => 'ok', 'detail' => $config['app']['name'] ?? 'app'];
$version  = trim((string) @file_get_contents(__DIR__ . '/Version.txt')) ?: 'unknown';

// 2. Database connection check (when required).
$requireDb = (bool) ($config['health']['require_database'] ?? true);
$dbStatus  = 'ok';
$dbDetail  = '';
$dbConnected = !$requireDb;

if ($requireDb) {
    try {
        $db  = $config['database'];
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $db['host'], $db['port'] ?? '3306', $db['database'], $db['charset'] ?? 'utf8mb4'
        );
        $pdo = new PDO($dsn, $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT            => 5,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->query('SELECT 1');
        $dbConnected = true;
        $dbDetail    = 'connected';
    } catch (Throwable $e) {
        $dbStatus = 'error';
        $dbDetail = $e->getMessage();
        $healthy  = false;
    }
}

$checks[] = ['name' => 'database', 'status' => $dbStatus, 'detail' => $dbDetail];

if ($asJson) {
    header('Content-Type: application/json');
    http_response_code($healthy ? 200 : 503);
    echo json_encode([
        'status'  => $healthy ? 'ok' : 'unhealthy',
        'version' => $version,
        'environment' => $config['app']['environment'] ?? 'unknown',
        'checks'  => $checks,
    ]);
    exit;
}

// CLI output.
foreach ($checks as $c) {
    printf("%-12s  %-6s  %s\n", $c['name'], $c['status'], $c['detail']);
}
printf("version:      %s\n", $version);
exit($healthy ? 0 : 1);