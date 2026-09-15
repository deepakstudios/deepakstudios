<?php

/**
 * Install.php
 *
 * Database installer + idempotent migration runner.
 *
 * Usage (CLI):  php Install.php
 * Also invoked automatically by deploy.php after every push to main.
 *
 * Safe to run multiple times:
 *  - executed migrations are tracked in the `migrations` table
 *  - a migration runs only once
 *  - running again never destroys existing data
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Install.php must be run from the command line only.\n");
}

require_once __DIR__ . '/config.php';
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/lib_migrations.php';

date_default_timezone_set('UTC');

try {
    ensureStorageDirectories($config);
    $pdo  = connectDb($config);
    $out  = runMigrations($pdo, getMigrations());

    $version = trim((string) @file_get_contents(__DIR__ . '/Version.txt')) ?: 'unknown';

    echo "Version:      {$version}\n";
    echo "Applied:      " . (empty($out['applied']) ? '(none)' : implode(', ', $out['applied'])) . "\n";
    echo "Skipped:      " . (empty($out['skipped']) ? '(none)' : implode(', ', $out['skipped'])) . "\n";
    echo "Newest migr.: {$out['newest_migration']}\n";
    echo "Migrations OK.\n";
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Migration failed: " . $e->getMessage() . "\n");
    exit(1);
}