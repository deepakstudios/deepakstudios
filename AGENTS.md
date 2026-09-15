# AI Project Memory (AGENTS.md)

This file is the permanent documentation and memory for AI agents working on this project. Any AI agent should be able to open ONLY this file and immediately understand the full project.

---

## Project Overview

- **Repository:** `deepakstudios/deepakstudios`
- **What it is:** A GitHub profile README repository that also hosts a PHP-based production deployment system.
- **Purpose:** Displays the GitHub profile page for **Deepak** (deepakstudios) and provides an event-driven, webhook-based deployment pipeline for production.
- **Stack:** Pure PHP (no framework). Markdown profile README. MySQL/MariaDB database.

---

## Architecture

### Repository Structure

```
/
├── AGENTS.md          # This file — AI agent memory
├── Version.txt        # Current semantic version
├── config.php         # Immutable environment configuration (NOT committed)
├── config.php.example # Template for config.php
├── Install.php        # CLI installer — runs migrations via lib_migrations.php
├── lib_migrations.php # Shared migrations registry + runner (Install.php + deploy.php)
├── deploy.php         # GitHub webhook deployment endpoint (git OR archive mode)
├── health.php         # Health check endpoint
├── server-setup.sh    # One-time server bootstrap (VPS/shell hosts)
├── index.html         # Live site (Deepak Studios homepage)
├── README.md          # GitHub profile README
├── .gitignore         # Protects secrets & runtime state
├── storage/
│   ├── deploy.lock    # Deployment lock file (created at runtime)
│   └── logs/
│       └── deployment.log  # Deployment history (created at runtime)
```

### How the application works

1. The GitHub profile README (`README.md`) is displayed on the user's public GitHub profile.
2. `index.html` is the live production website (Deepak Studios — cinematic wedding photography).
3. PHP scripts (`Install.php`, `lib_migrations.php`, `deploy.php`, `health.php`) provide the deployment framework:
   - **lib_migrations.php** holds the migrations registry and the idempotent runner (`runMigrations()`, `connectDb()`, `ensureStorageDirectories()`).
   - **Install.php** CLI entry point — runs all pending migrations.
   - **deploy.php** receives GitHub webhooks, verifies signatures, and deploys `main` to production using either method:
     - `archive` (default) — pure PHP, no shell/git needed, works on shared hosting; downloads the GitHub zip and swaps files while preserving `config.php` + `storage/`.
     - `git` — uses the git binary via shell_exec (VPS/dedicated hosts).
   - **health.php** verifies the application and database are operational.
4. `server-setup.sh` is the one-time bootstrap: clones the repo, creates `config.php`, runs migrations, and prints the GitHub webhook setup steps.

---

## Database

- **Engine:** MySQL/MariaDB (via PDO)
- **Connection:** Configured in `config.php` (never committed to git)
- **Tables created by migrations:**

### `migrations`
Tracks which migrations have been executed.

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PRIMARY KEY |
| migration | VARCHAR(255) | Migration identifier (e.g. `001_initial_schema`) |
| executed_at | DATETIME | When the migration ran |

### `deployment_logs`
Records deployment history (written by `deploy.php`).

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PRIMARY KEY |
| started_at | DATETIME | Deployment start |
| finished_at | DATETIME | Deployment end |
| previous_version | VARCHAR(20) | Prior deployed version |
| new_version | VARCHAR(20) | Version after deployment |
| previous_commit | VARCHAR(40) | Prior commit hash |
| new_commit | VARCHAR(40) | Commit hash after fetch |
| migration_result | VARCHAR(255) | Result of migrations |
| status | VARCHAR(20) | success / failure |
| error_message | TEXT | Error details (no secrets) |

### Migration system

- Migrations are idempotent — running `php Install.php` multiple times is safe.
- Each migration runs exactly once (tracked in `migrations` table).
- Migration identifiers follow the pattern: `NNN_description` (e.g. `001_initial_schema`).
- **NEVER modify an already-executed migration.** Add a new one instead.
- Current migrations:

| ID | Description |
|---|---|
| 001_initial_schema | Creates `migrations` and `deployment_logs` tables |

---

## Configuration System

- `config.php` holds environment-specific credentials (database, app URL, environment, deployment webhook secret).
- `config.php` **must NEVER** be modified by the deployment process.
- `config.php` is git-ignored — it is **NOT** committed to the repository.
- A `config.php.example` template IS committed so fresh servers know the required structure.
- Credentials must never be hard-coded anywhere else in the project.
- Real production credentials must never be committed to GitHub.

---

## Deployment

### How updates reach production (event-driven, no cron)

```
Developer / AI Agent
        |
        v
GitHub main branch (push)
        |
        v
GitHub webhook (push event)
        |
        v
Production server /deploy.php
        |
        v
HMAC signature verification
        |
        v
Branch == main check
        |
        v
Acquire deployment lock
        |
        v
git fetch origin main
        |
        v
Update working tree to origin/main
        |
        v
Run Install.php (migrations)
        |
        v
Validate health check
        |
        v
Release lock & log deployment
        |
        v
Application updated
```

### Deployment endpoint

- **URL:** `https://YOUR-DOMAIN/deploy.php`
- **Method:** POST
- **Secured by:** GitHub webhook HMAC-SHA256 signature (secret from `config.php`)
- **Behavior:**
  1. Rejects any request without a valid `X-Hub-Signature-256`.
  2. Rejects non-`push` events.
  3. Rejects pushes to any branch except `main`.
  4. Acquires a deployment lock (single deployment at a time).
  5. Deploys `main` via the configured method (`archive` or `git`).
  6. Runs pending migrations (in-process — no shell needed).
  7. Validates the database connection (health).
  8. Logs the deployment in the database (`deployment_logs`) and to `storage/logs/deployment.log`.
  9. Returns JSON response.

### Deployment methods

- **`archive` (default, shared-hosting friendly):** `deploy.php` downloads
  `https://codeload.github.com/{owner}/{repo}/zip/refs/heads/main`, extracts it, and syncs
  every file EXCEPT `config.php` and `storage/`. Pure PHP (cURL or streams + ZipArchive) —
  works on cPanel/etc. where shell and git are disabled. Database migrations run in-process.
- **`git` (VPS/dedicated):** `deploy.php` runs `git fetch origin main` + `git reset --hard origin/main`
  via shell_exec. Migrations run in-process too.

Both modes are triggered *only* by verified push webhooks — no cron, and arbitrary commits/branches
from the payload are never trusted or executed.

### Production setup for GitHub webhook

```text
1. Server: install PHP 8.x + PDO MySQL, git, and make the PHP user able to run `git` commands.
2. Create `config.php` from `config.php.example` and fill in real values.
3. In GitHub → repo → Settings → Webhooks → Add webhook:
   - Payload URL: https://YOUR-DOMAIN/deploy.php
   - Content type: application/json
   - Secret: (same value as `webhook.secret` in config.php)
   - Events: Just the push event
4. Point the web root to this directory (or copy it there).
```

### Zero-downtime consideration

The current server architecture is unknown/fresh. The safe practical strategy used here deploys in place with a fetch + reset workflow (`git fetch origin main` then `git reset --hard origin/main`). See `AGENTS.md` → "Important decisions" for notes. If the production setup later supports it, a `releases/` + `current` symlink strategy can be adopted without breaking this design.

---

## Security Requirements

- HMAC-SHA256 webhook signature verification is **mandatory** (constant-time comparison).
- HTTPS is required on the production server.
- `config.php` must never be exposed by the web server.
- No credentials in git, logs, or shell commands.
- Deployment lock prevents concurrent deployments.
- Shell commands are built with `escapeshellarg()` — no untrusted payload values are interpolated.
- Only `origin/main` is deployable. Arbitrary commits from webhooks are never executed.

---

## How to run/test the project

### Local testing (PHP CLI)

```bash
# Syntax-check all PHP files
php -l config.php
php -l Install.php
php -l deploy.php
php -l health.php

# Run database migrations (requires config.php with working DB)
php Install.php

# Check health (requires running web server / config)
php health.php
```

### Test a webhook locally (optional)

```bash
# Generate a valid signature for the secret 'testsecret'
php -r "echo hash_hmac('sha256', file_get_contents('php://stdin'), 'testsecret');"
```

Requires **PHP 8.0+** with PDO MySQL (`extension=pdo_mysql`).

---

## Versioning

### Current Version

```
1.0.6
```

### Semver rules

- **Bug fix** → PATCH (1.0.1)
- **Backward-compatible feature** → MINOR (1.1.0)
- **Breaking change** → MAJOR (2.0.0)

### How to update the version

1. Update `Version.txt`.
2. Update `AGENTS.md` (current version + change log).
3. Add a change log entry.
4. Commit everything together.

---

## Change Log

## 1.0.6 - 2026-09-15

- Reverted demo heading back to "Deepak Studios" (demo verification complete).

## 1.0.5 - 2026-09-15

- Re-verification push to confirm end-to-end auto-deploy: server was still on 1.0.3
  after `pdo_mysql` + `zip` extensions were installed. This commit exercises the full
  webhook pipeline (health.php now reports DB connected + all endpoints responsive).

## 1.0.4 - 2026-09-15

- Demo change to test the auto-deploy pipeline: hero heading in `index.html` changed to "Satya Studios".

## 1.0.3 - 2026-09-15

- `server-setup.sh` now REFUSES to run in a directory that is already a git repo with a different
  origin (prevents accidentally fetching/pushing the wrong project, e.g. a panel's existing repo).
- Also refuses non-empty directories (unless they are OUR repo). Use a fresh web-root dir instead.

## 1.0.2 - 2026-09-15

- Fixed `server-setup.sh` for the `curl | bash` one-liner: webhook secret is now auto-generated
  (openssl/urandom) instead of an interactive `read` prompt (stdin is consumed by `bash`, so prompts broke).
- The setup script now defaults to the current directory as web root (`PWD`) instead of `/var/www/html`.
- Added web-server ownership/permissions step so the webhook can write the web root (BaoTa/cPanel compatible).
- Migrations are non-fatal during setup (they auto-run on the first verified push).

## 1.0.1 - 2026-09-15

- Added `lib_migrations.php` — shared migration registry + idempotent runner, used by both `Install.php` and `deploy.php`.
- Upgraded `deploy.php` with dual deployment methods:
  - `archive` (default) — pure PHP deploy via GitHub zip download + file swap. Works on shared hosting (cPanel) with NO shell/git. Preserves `config.php` and `storage/`.
  - `git` — original shell-based fetch/reset for VPS/dedicated hosts.
- Migrations and health validation now run in-process (no shell dependency).
- Added `server-setup.sh` — one-time server bootstrap command.
- Added `index.html` — live Deepak Studios website.
- Updated `config.php.example` with `deploy.method`, `deploy.owner`, `deploy.repo`, `deploy.verify_ssl`.
- Deployment remains fully event-driven via GitHub webhooks — **no cron**.

## 1.0.0 - 2026-09-15

- Initial deployment architecture.
- Added `AGENTS.md` (AI project memory).
- Added `Version.txt` (1.0.0).
- Added `config.php.example` (template; `config.php` is git-ignored).
- Added `Install.php` with idempotent migration runner and `migrations` table.
- Added migration `001_initial_schema` (creates `migrations` + `deployment_logs` tables).
- Added `deploy.php` secure GitHub webhook deployment endpoint.
- Added `health.php` health-check endpoint.
- Added deployment locking (`storage/deploy.lock`, auto-expiring).
- Added deployment logging to database and file.
- Added `.gitignore` protecting `config.php`, storage, and runtime state.
- Deployment is event-driven via GitHub webhooks — **no cron**.

---

## Known Issues / Technical Debt

1. **Archive mode does not delete removed files:** `deploy.php` in `archive` mode copies/overwrites files from the GitHub zip but does not delete files that were removed from the repo (avoids accidentally deleting unrelated web-root files on shared hosting). If a file is removed from the repo, delete leftover copies from production manually once.
2. **Working-tree reset (git mode):** The `git` method uses `git reset --hard origin/main` — reliable but not zero-downtime for long-running requests.
3. **Git user for webhook (git mode):** The PHP/web server user must have rights to run `git` commands and write to the repository directory.
4. **Web server configuration:** The live site needs HTTPS and PHP 8+ with `pdo_mysql` enabled. Must be configured by the owner on the hosting provider.
5. **Database:** Migrations require a live MySQL/MariaDB accessible from the server; no DB runs locally.
6. **SSL on Windows test setups:** HTTPS downloads need a CA bundle; production Linux/shared hosts already ship one.

---

## Things AI Agents MUST NOT Change

- **NEVER modify already-executed migrations** (anything in the `migrations` table). Add a new migration instead.
- **NEVER commit `config.php`** or any real credentials.
- **NEVER rewrite git history or force-push**.
- **NEVER create branches**; all work goes directly to `main`.
- **NEVER modify deployment to use cron/scheduled polling.**
- **NEVER remove the webhook signature verification** from `deploy.php`.
- **NEVER hard-code credentials** anywhere.
- **NEVER log secrets** (passwords, keys, tokens).

---

## How to safely modify the project

1. Read this file, `Version.txt`, and `config.php` (if present) first.
2. Understand the current state before changing code.
3. Implement the change directly on `main`.
4. Syntax-check modified PHP files (`php -l`).
5. Update `AGENTS.md` (change log + any architecture notes).
6. Update `Version.txt` if it is a meaningful release (semver).
7. Commit to `main` and push to `origin main`.
8. The GitHub webhook automatically deploys to production. No manual/cron steps.