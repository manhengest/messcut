#!/usr/bin/env bash
# Deploy the MESSCUT theme to Hosting Ukraine over SSH.
# Uses tar-over-SSH (not rsync): macOS openrsync breaks against GNU rsync on
# shared hosting, and expect-as-rsh corrupts the rsync binary protocol.
#
# Usage:
#   ./scripts/deploy.sh              # build CSS, then upload the theme
#   ./scripts/deploy.sh --dry-run    # list files that would be uploaded
#   ./scripts/deploy.sh discover     # find WordPress root on the server

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
THEME_SRC="$ROOT/wp-content/themes/messcut"
ENV_FILE="$ROOT/.env.deploy"
MUX=""

usage() {
  cat <<'EOF'
Deploy the messcut theme (not WordPress core, plugins, or uploads).

  ./scripts/deploy.sh              build CSS + upload theme
  ./scripts/deploy.sh --dry-run    list files, upload nothing
  ./scripts/deploy.sh discover     list remote wp-config.php paths
  ./scripts/deploy.sh --help

Credentials live in .env.deploy (gitignored). Copy from .env.deploy.example.
EOF
}

die() {
  echo "error: $*" >&2
  exit 1
}

load_env() {
  [[ -f "$ENV_FILE" ]] || die "missing $ENV_FILE — copy .env.deploy.example and fill it in"
  # shellcheck disable=SC1090
  source "$ENV_FILE"
  [[ -n "${DEPLOY_HOST:-}" ]] || die "DEPLOY_HOST is empty"
  [[ -n "${DEPLOY_USER:-}" ]] || die "DEPLOY_USER is empty"
  DEPLOY_PORT="${DEPLOY_PORT:-22}"
  if [[ "${DEPLOY_PATH:-}" == *"'"* ]]; then
    die "DEPLOY_PATH must not contain a single quote"
  fi
}

ssh_plain() {
  ssh -p "$DEPLOY_PORT" \
    -o ControlMaster=no \
    -o ControlPath="$MUX" \
    -o StrictHostKeyChecking=accept-new \
    "${DEPLOY_USER}@${DEPLOY_HOST}" "$@"
}

stop_mux() {
  if [[ -n "$MUX" && -S "$MUX" ]]; then
    ssh -p "$DEPLOY_PORT" -o ControlPath="$MUX" -O exit \
      "${DEPLOY_USER}@${DEPLOY_HOST}" >/dev/null 2>&1 || true
    rm -f "$MUX"
  fi
}

start_mux() {
  MUX="${TMPDIR:-/tmp}/messcut-deploy.$$.sock"
  trap stop_mux EXIT

  echo "Connecting to ${DEPLOY_USER}@${DEPLOY_HOST}…"
  if [[ -n "${DEPLOY_PASS:-}" ]]; then
    DEPLOY_PASS="$DEPLOY_PASS" "$ROOT/scripts/ssh-pass.exp" \
      -C \
      -o ControlMaster=yes \
      -o ControlPath="$MUX" \
      -o ControlPersist=120 \
      -o ServerAliveInterval=30 \
      -p "$DEPLOY_PORT" \
      -fN \
      "${DEPLOY_USER}@${DEPLOY_HOST}"
  else
    ssh -C -p "$DEPLOY_PORT" \
      -o StrictHostKeyChecking=accept-new \
      -o ControlMaster=yes \
      -o ControlPath="$MUX" \
      -o ControlPersist=120 \
      -o ServerAliveInterval=30 \
      -fN \
      "${DEPLOY_USER}@${DEPLOY_HOST}"
  fi

  local ok=0
  local i
  for i in 1 2 3 4 5 6 7 8; do
    if ssh -p "$DEPLOY_PORT" -o ControlPath="$MUX" -O check \
      "${DEPLOY_USER}@${DEPLOY_HOST}" >/dev/null 2>&1; then
      ok=1
      break
    fi
    sleep 0.25
  done
  [[ "$ok" == 1 ]] || die "SSH connection failed. ControlMaster may be disabled on this host."
}

remote() {
  ssh_plain "$@"
}

# Archive the theme. COPYFILE_DISABLE skips macOS AppleDouble (._*) forks.
theme_tar() {
  COPYFILE_DISABLE=1 tar -C "$THEME_SRC" \
    --no-xattrs \
    --no-mac-metadata \
    --exclude='node_modules' \
    --exclude='assets/scss' \
    --exclude='.git' \
    --exclude='browser-sync.config.js' \
    --exclude='.DS_Store' \
    --exclude='*.map' \
    -cf - .
}

discover() {
  start_mux
  echo "Looking for wp-config.php on ${DEPLOY_USER}@${DEPLOY_HOST}…"
  echo
  remote 'find "$HOME" /home /var/www /www -name wp-config.php 2>/dev/null | sed "s|/wp-config.php||" | sort -u'
  echo
  echo "Set DEPLOY_PATH in .env.deploy to one of those directories (the WordPress root),"
  echo "then run: ./scripts/deploy.sh --dry-run"
}

build_css() {
  [[ -d "$THEME_SRC" ]] || die "theme not found at $THEME_SRC"
  command -v npm >/dev/null || die "npm is not installed"
  echo "Building production CSS…"
  (cd "$THEME_SRC" && npm run build)
}

deploy() {
  local dry_run="${1:-}"
  [[ -n "${DEPLOY_PATH:-}" ]] || die "DEPLOY_PATH is empty — run ./scripts/deploy.sh discover, then set it in .env.deploy"

  local remote_themes="${DEPLOY_PATH%/}/wp-content/themes"
  local dest="${remote_themes}/messcut/"

  build_css

  echo
  echo "Theme archive contents:"
  theme_tar | tar -tf - | sed 's|^|  |'
  echo

  if [[ "$dry_run" == "1" ]]; then
    echo "Dry run — nothing uploaded."
    echo "Would upload to ${DEPLOY_USER}@${DEPLOY_HOST}:${dest}"
    return
  fi

  start_mux

  remote "test -d '${DEPLOY_PATH%/}/wp-content/themes' || mkdir -p '${DEPLOY_PATH%/}/wp-content/themes'"

  echo "Uploading theme → ${DEPLOY_USER}@${DEPLOY_HOST}:${dest}"
  theme_tar | remote "
    set -e
    cd '$remote_themes'
    rm -rf messcut.deploy-new messcut.deploy-old
    mkdir messcut.deploy-new
    tar -xf - -C messcut.deploy-new
    if [ -d messcut ]; then mv messcut messcut.deploy-old; fi
    mv messcut.deploy-new messcut
    rm -rf messcut.deploy-old
  "

  echo "Theme uploaded."
  echo "If permalinks 404, open WP admin → Settings → Permalinks → Save."
}

main() {
  local cmd="${1:-deploy}"
  case "$cmd" in
    -h|--help) usage; exit 0 ;;
    discover)
      load_env
      discover
      ;;
    --dry-run)
      load_env
      deploy 1
      ;;
    deploy|"")
      load_env
      deploy 0
      ;;
    *)
      usage
      die "unknown argument: $cmd"
      ;;
  esac
}

main "${1:-}"
