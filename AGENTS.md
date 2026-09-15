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
├── Install.php        # Database installer + migration runner (idempotent)
├── deploy.php         # GitHub webhook deployment endpoint
├── health.php         # Health check endpoint
├── README.md          # GitHub profile README
├── .gitignore         # Protects secrets & runtime state
├── storage/
│   ├── deploy.lock    # Deployment lock file (created at runtime)
│   └── logs/
│       └── deployment.log  # Deployment history (created at runtime)
```

### How the application works

1. The GitHub profile README (`README.md`) is displayed on the user's public GitHub profile.
2. PHP scripts (`Install.php`, `deploy.php`, `health.php`) provide the application framework:
   - **Install.php** initializes the database schema via idempotent migrations.
   - **deploy.php** receives GitHub webhooks, verifies signatures, and deploys `origin/main` to production.
   - **health.php** verifies the application and database are operational.

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
  5. Fetches `origin/main`, resets the working tree.
  6. Runs `Install.php` for database migrations.
  7. Runs health validation.
  8. Logs the deployment in the database (`deployment_logs`).
  9. Returns JSON response.

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
1.0.0
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

1. **Working-tree reset:** The safest in-place strategy used (`git reset --hard origin/main`) is reliable but not zero-downtime for long-running requests. Acceptable for the current architecture.
2. **Git user for webhook:** The PHP/web server user must have rights to run `git` commands and write to the repository directory. On shared hosting this may require configuring `sudo` for the specific user.
3. **Web server configuration:** Local env has no web server; server config (Apache/nginx + PHP 8 + HTTPS) must be configured by the owner on the production host.
4. **Database:** No database is configured locally; migrations require a live MySQL/MariaDB accessible from the server.

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