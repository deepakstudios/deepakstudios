<?php

/**
 * lib_migrations.php
 *
 * Shared migration logic used by both Install.php (CLI) and deploy.php (webhook).
 *
 * NEVER edit or remove a migration that has already run on production.
 * Append new migrations at the END of the array in getMigrations().
 */

declare(strict_types=1);

if (!function_exists('getMigrations')) {
    /**
     * Migrations registry. Each entry: 'unique_id' => callable(PDO $pdo): void
     */
    function getMigrations(): array
    {
        return [
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
    }
}

if (!function_exists('connectDb')) {
    /**
     * Establish the PDO connection with safe defaults.
     */
    function connectDb(array $config): PDO
    {
        $db = $config['database'];

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $db['host'],
            $db['port'] ?? '3306',
            $db['database'],
            $db['charset'] ?? 'utf8mb4'
        );

        return new PDO($dsn, $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
}

if (!function_exists('ensureStorageDirectories')) {
    /**
     * Create storage directories used at runtime.
     */
    function ensureStorageDirectories(array $config): void
    {
        $repoPath = $config['deploy']['repo_path'] ?? __DIR__;
        $dirs = [
            dirname($config['deploy']['lock_file'] ?? $repoPath . '/storage/deploy.lock'),
            dirname($config['deploy']['log_file'] ?? $repoPath . '/storage/logs/deployment.log'),
        ];
        foreach ($dirs as $dir) {
            if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
                throw new RuntimeException("Cannot create storage directory: {$dir}");
            }
        }
    }
}

if (!function_exists('runMigrations')) {
    /**
     * Run all pending migrations. Safe to call repeatedly.
     *
     * @param PDO   $pdo        Active database connection
     * @param array $migrations Migration registry (see getMigrations())
     * @return array{applied: list<string>, skipped: list<string>, newest_migration: ?string}
     */
    function runMigrations(PDO $pdo, array $migrations): array
    {
        $result = ['applied' => [], 'skipped' => [], 'newest_migration' => null];

        foreach (array_keys($migrations) as $name) {
            $result['newest_migration'] = $name;
        }

        try {
            $pdo->exec(
                "CREATE TABLE IF NOT EXISTS migrations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    migration VARCHAR(255) NOT NULL UNIQUE,
                    executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Failed to create migrations table: ' . $e->getMessage());
        }

        $stmt = $pdo->query('SELECT migration FROM migrations');
        $executed = array_column($stmt->fetchAll(), 'migration');

        foreach ($migrations as $name => $fn) {
            if (in_array($name, $executed, true)) {
                $result['skipped'][] = $name;
                continue;
            }

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
}