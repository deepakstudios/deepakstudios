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

TARGET_DIR="${1:-/var/www/html}"
if [ "${TARGET_DIR}" = "/" ] || [ "${TARGET_DIR}" = "." ]; then
  echo "Refusing to deploy into ${TARGET_DIR}. Pick a real web root, e.g. /var/www/html" >&2
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

# 2) Webhook secret (prompt if not given via env)
if [ -z "${WEBHOOK_SECRET:-}" ]; then
  read -r -p "=== Webhook secret (a long random string — save this, you'll paste it into GitHub): " WEBHOOK_SECRET
fi
if [ -z "${WEBHOOK_SECRET// }" ]; then
  echo "Secret cannot be empty." >&2
  exit 1
fi

# 3) Clone the repo
echo "==> [2/6] Cloning/updating the repository into ${TARGET_DIR}..."
if [ -d "${TARGET_DIR}/.git" ]; then
  git -C "${TARGET_DIR}" fetch origin "${BRANCH}"
  git -C "${TARGET_DIR}" reset --hard "origin/${BRANCH}"
else
  mkdir -p "$(dirname "${TARGET_DIR}")"
  git clone --branch "${BRANCH}" "${GITHUB_URL}" "${TARGET_DIR}"
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

# 5) Run migrations
echo "==> [4/6] Running database migrations..."
(cd "${TARGET_DIR}" && php Install.php)

# 6) Done
echo
echo "==> [5/6] Deployment method"
if command -v sudo >/dev/null 2>&1; then
  echo "    You have shell access, so this script keeps method=git (see config.php)."
else
  echo "    No shell/sudo detected — set 'method' => 'archive' in config.php for pure-PHP deploys."
fi
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