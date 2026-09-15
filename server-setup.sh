#!/usr/bin/env bash
# =============================================================================
# Deepak Studios — one-time server setup
#
# Run ONCE on your production server. After this:
#   * every push to GitHub `main` automatically updates your live site
#   * database migrations run automatically
#   * no cron, no manual steps
#
# Usage (on a Linux server with shell access):
#   curl -fsSL https://raw.githubusercontent.com/deepakstudios/deepakstudios/main/server-setup.sh | bash
#
# On shared hosting (cPanel) WITHOUT shell: skip this script.
# Instead follow the webhook steps printed at the end, or see AGENTS.md.
# =============================================================================
set -euo pipefail

GITHUB_USER="deepakstudios"
GITHUB_REPO="deepakstudios"
BRANCH="main"
GITHUB_URL="https://github.com/${GITHUB_USER}/${GITHUB_REPO}.git"

# Target web root: arg 1 > $TARGET_DIR env > current directory.
TARGET_DIR="${1:-${TARGET_DIR:-$(pwd)}}"
if [ "${TARGET_DIR}" = "/" ] || [ "${TARGET_DIR}" = "." ]; then
  echo "Refusing to deploy into ${TARGET_DIR}. cd into your web root (e.g. /www/wwwroot/deepakstudios.in) first." >&2
  exit 1
fi

echo "==> Deepak Studios auto-deployment setup"
echo "    Repo:    ${GITHUB_URL}"
echo "    Target:  ${TARGET_DIR}"
echo

# 1) System dependencies
echo "==> [1/6] Checking dependencies (php, git, unzip)..."
for bin in php git unzip; do
  if ! command -v "${bin}" >/dev/null 2>&1; then
    echo "    ${bin} not found. Install with:  sudo apt install php-cli git unzip" >&2
    echo "    (or the equivalent for your distribution) and re-run." >&2
    exit 1
  fi
done
echo "    Dependencies OK."

# 2) Webhook secret: use $WEBHOOK_SECRET if provided, otherwise auto-generate one.
#    (Auto-generation is required so the `curl | bash` one-liner works.)
if [ -z "${WEBHOOK_SECRET:-}" ]; then
  WEBHOOK_SECRET="$( (openssl rand -hex 32 2>/dev/null || tr -dc 'A-Za-z0-9' < /dev/urandom | head -c 32) || true )"
fi
if [ -z "${WEBHOOK_SECRET// }" ]; then
  echo "Could not generate a webhook secret. Install openssl or set WEBHOOK_SECRET=your-secret." >&2
  exit 1
fi

# 3) Clone the repo
echo "==> [2/6] Cloning/updating the repository into ${TARGET_DIR}..."
OUR_REMOTE="https://github.com/${GITHUB_USER}/${GITHUB_REPO}.git"

if [ -d "${TARGET_DIR}/.git" ]; then
  CURRENT_ORIGIN="$(git -C "${TARGET_DIR}" remote get-url origin 2>/dev/null || true)"
  if [ "${CURRENT_ORIGIN}" != "${OUR_REMOTE}" ]; then
    echo "    ABORT: ${TARGET_DIR} is already a git repo with origin: ${CURRENT_ORIGIN}" >&2
    echo "    This deploy owns ONLY https://github.com/${GITHUB_USER}/${GITHUB_REPO}.git" >&2
    echo "    Do NOT run it in an existing project folder. Use a fresh web-root directory:" >&2
    echo "      mkdir -p /www/wwwroot/deepakstudios.in && cd /www/wwwroot/deepakstudios.in" >&2
    echo "      curl -fsSL https://raw.githubusercontent.com/deepakstudios/deepakstudios/main/server-setup.sh | bash" >&2
    exit 1
  fi
  git -C "${TARGET_DIR}" fetch origin "${BRANCH}"
  git -C "${TARGET_DIR}" reset --hard "origin/${BRANCH}"
elif [ -n "$(ls -A "${TARGET_DIR}" 2>/dev/null || true)" ]; then
  echo "    ABORT: ${TARGET_DIR} is not empty." >&2
  echo "    Run this from a fresh directory, e.g. /www/wwwroot/deepakstudios.in" >&2
  exit 1
else
  mkdir -p "${TARGET_DIR}"
  git clone --branch "${BRANCH}" "${OUR_REMOTE}" "${TARGET_DIR}"
fi

# 4) Create config.php from the example if it does not exist
echo "==> [3/6] Creating config.php (ONLY if missing)..."
if [ -f "${TARGET_DIR}/config.php" ]; then
  echo "    config.php exists — leaving it untouched (credentials preserved)."
else
  sed -e "s|127.0.0.1|${DB_HOST:-localhost}|" \
      -e "s|'database' => 'deepakstudios'|'database' => '${DB_NAME:-deepakstudios}'|" \
      -e "s|'username' => 'db_user'|'username' => '${DB_USER:-db_user}'|" \
      -e "s|'password' => 'CHANGE_ME'|'password' => '${DB_PASS:-CHANGE_ME}'|" \
      -e "s|CHANGE_ME_TO_A_LONG_RANDOM_STRING|${WEBHOOK_SECRET}|" \
      "${TARGET_DIR}/config.php.example" > "${TARGET_DIR}/config.php"
  chmod 600 "${TARGET_DIR}/config.php"
  echo "    config.php created. Edit ${TARGET_DIR}/config.php to set real DB credentials."
fi

# 5) Run database migrations (non-fatal: they auto-run on the first push anyway)
echo "==> [4/6] Running database migrations..."
if (cd "${TARGET_DIR}" && php Install.php); then
  echo "    Migrations OK."
else
  echo "    WARNING: migrations could not complete (database not ready yet?)."
  echo "    They will run automatically on the first GitHub push if a database is configured."
fi

# 6) Make the web root writable by the web server so the webhook can auto-update
echo "==> [5/6] Setting web-server ownership/permissions..."
WEB_USER="$(ps -o user= -C php-fpm 2>/dev/null | tr -d ' ' | sort -u | grep -v '^$' | head -1)"
if [ -z "${WEB_USER}" ]; then
  for u in www www-data; do
    if id "${u}" >/dev/null 2>&1; then WEB_USER="${u}"; break; fi
  done
fi
if [ -z "${WEB_USER}" ]; then
  echo "    Could not detect web-server user; set ownership manually."
  echo "    Then set config 'method' => 'archive' OR run commands as that web user."
else
  if [ "$(id -u)" = "0" ]; then
    chown -R "${WEB_USER}:${WEB_USER}" "${TARGET_DIR}"
    chmod -R u+rwX,g+rwX,o-w "${TARGET_DIR}"
    echo "    Ownership set to ${WEB_USER} (web server)."
  else
    sudo chown -R "${WEB_USER}:${WEB_USER}" "${TARGET_DIR}" 2>/dev/null \
      && sudo chmod -R u+rwX,g+rwX,o-w "${TARGET_DIR}" 2>/dev/null \
      && echo "    Ownership set to ${WEB_USER} (web server)." \
      || echo "    Run as root to auto-set ownership, or chown to ${WEB_USER} yourself."
  fi
fi

# 6) Done
echo
echo "==> [6/6] ALL DONE. One last manual step — the GitHub webhook:"
echo
echo "   ┌──────────────────────────────────────────────────────────────────┐"
echo "   │  1. Open:  https://github.com/${GITHUB_USER}/${GITHUB_REPO}/settings/hooks"
echo "   │  2. Click  Add webhook"
echo "   │  3. Payload URL:  https://YOUR-DOMAIN/deploy.php"
echo "   │  4. Content type: application/json"
echo "   │  5. Secret:       ${WEBHOOK_SECRET}"
echo "   │  6. Select:       Just the push event"
echo "   │  7. Add webhook"
echo "   └──────────────────────────────────────────────────────────────────┘"
echo
echo "After that, EVERY push to GitHub '${BRANCH}' auto-deploys the live site."
echo "Verify with:  https://YOUR-DOMAIN/health.php"