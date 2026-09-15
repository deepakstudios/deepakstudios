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

date_default_timezone_set('UTC');

/**
 * Migrations registry.
 *
 * NEVER edit or remove a migration that has already run on production.
 * Append new migrations at the END of this list.
 *
 * Each entry: 'unique_id' => callable(PDO $pdo): void
 */
$migrations = [
    '001_initial_schema' => function (PDO $pdo): void {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS deployment_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                started_at DATETIME NOT NULL,
                finished_at DATETIME NULL,
                previous_version VARCHAR(20) NULL,
                new_version VARCHAR(20) NULL,
                previous_commit VARCHAR(64) NULL,
                new_commit VARCHAR(64) NULL,
                migration_result VARCHAR(255) NULL,
                status VARCHAR(20) NOT NULL,
                error_message TEXT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    },
];

/**
 * Create storage directories used at runtime.
 */
function ensureStorageDirectories(array $config): void
{
    $dirs = [
        dirname($config['deploy']['lock_file'] ?? __DIR__ . '/storage/deploy.lock'),
        dirname($config['deploy']['log_file'] ?? __DIR__ . '/storage/logs/deployment.log'),
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            throw new RuntimeException("Cannot create storage directory: {$dir}");
        }
    }
}

/**
 * Establish the PDO connection with safe defaults.
 */
function connect(array $config): PDO
{
    $db = $config['database'];

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        $db['host'],
        $db['port'] ?? '3306',
        $db['database'],
        $db['charset'] ?? 'utf8mb4'
    );

    $pdo = new PDO($dsn, $db['username'], $db['password'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    return $pdo;
}

/**
 * Run all pending migrations. Safe to call repeatedly.
 */
function runMigrations(PDO $pdo, array $migrations): array
{
    $result = ['applied' => [], 'skipped' => [], 'newest_migration' => null];

    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL UNIQUE,
            executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (PDOException $e) {
        throw new RuntimeException('Failed to create migrations table: ' . $e->getMessage());
    }

    $stmt = $pdo->query('SELECT migration FROM migrations');
    $executed = array_column($stmt->fetchAll(), 'migration');

    foreach ($migrations as $name => $fn) {
        $result['newest_migration'] = $name;

        if (in_array($name, $executed, true)) {
            $result['skipped'][] = $name;
            continue;
        }

        // Wrap each migration in a transaction; abort on failure.
        $pdo->beginTransaction();
        try {
            $fn($pdo);
            $insert = $pdo->prepare(
                'INSERT INTO migrations (migration, executed_at) VALUES (:name, NOW())'
            );
            $insert->execute([':name' => $name]);
            $pdo->commit();
            $result['applied'][] = $name;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw new RuntimeException(
                "Migration {$name} failed and was rolled back: " . $e->getMessage()
            );
        }
    }

    return $result;
}

try {
    ensureStorageDirectories($config);
    $pdo  = connect($config);
    $out  = runMigrations($pdo, $migrations);

    $version = trim((string) @file_get_contents(__DIR__ . '/Version.txt')) ?: 'unknown';

    echo "Version:      {$version}\n";
    echo "Applied:      " . (empty($out['applied']) ? '(none)' : implode(', ', $out['applied'])) . "\n";
    echo "Skipped:      " . (empty($out['skipped']) ? '(none)' : implode(', ', $out['skipped'])) . "\n";
    echo "Newest migh.: {$out['newest_migration']}\n";
    echo "Migrations OK.\n";
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Migration failed: " . $e->getMessage() . "\n");
    exit(1);
}