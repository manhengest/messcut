#!/usr/bin/env bash
# Push local WordPress posts + ACF text options to production (by slug).
# Does not replace the database, uploads, leads, users, or media fields.
#
# Usage:
#   ./scripts/sync-wp-content.sh           # export local Docker WP + apply on host
#   ./scripts/sync-wp-content.sh --dry-run # export only; print summary

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$ROOT/.env.deploy"
EXPORT_PHP="$ROOT/scripts/export-wp-content.php"
APPLY_PHP="$ROOT/scripts/apply-wp-content.php"
PAYLOAD="${TMPDIR:-/tmp}/messcut-content.$$.json"
MUX=""

usage() {
  cat <<'EOF'
Sync MESSCUT editorial content (posts + ACF options) to Hosting Ukraine.

  ./scripts/sync-wp-content.sh           export from local Docker + apply on server
  ./scripts/sync-wp-content.sh --dry-run export locally; upload nothing

Requires Docker (local export), .env.deploy (SSH), and DEPLOY_PATH on the server.
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
  [[ -n "${DEPLOY_PATH:-}" ]] || die "DEPLOY_PATH is empty — run ./scripts/deploy.sh discover"
  DEPLOY_PORT="${DEPLOY_PORT:-22}"
  if [[ "${DEPLOY_PATH}" == *"'"* ]]; then
    die "DEPLOY_PATH must not contain a single quote"
  fi
}

stop_mux() {
  if [[ -n "$MUX" && -S "$MUX" ]]; then
    ssh -p "$DEPLOY_PORT" -o ControlPath="$MUX" -O exit \
      "${DEPLOY_USER}@${DEPLOY_HOST}" >/dev/null 2>&1 || true
    rm -f "$MUX"
  fi
}

start_mux() {
  MUX="${TMPDIR:-/tmp}/messcut-content-sync.$$.sock"
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
  [[ "$ok" == 1 ]] || die "SSH connection failed"
}

export_local() {
  [[ -f "$EXPORT_PHP" ]] || die "missing $EXPORT_PHP"
  command -v docker >/dev/null || die "docker is not installed"
  echo "Exporting content from local WordPress…"
  docker compose -f "$ROOT/docker-compose.yml" run --rm \
    -v "$ROOT/scripts:/opt/messcut-scripts:ro" \
    wpcli eval-file /opt/messcut-scripts/export-wp-content.php >"$PAYLOAD"
  python3 - "$PAYLOAD" <<'PY'
import json, sys
p = json.load(open(sys.argv[1]))
opts = p.get('options') or {}
en = [k for k in opts if str(k).startswith('en_')]
print(f"  posts: {len(p.get('posts', []))}")
print(f"  options: {len(opts)} (en: {len(en)})")
print(f"  content_version: {p.get('content_version')}")
PY
}

apply_remote() {
  [[ -f "$APPLY_PHP" ]] || die "missing $APPLY_PHP"
  start_mux
  echo "Uploading payload…"
  scp -P "$DEPLOY_PORT" -o ControlPath="$MUX" \
    "$PAYLOAD" "${DEPLOY_USER}@${DEPLOY_HOST}:/tmp/messcut-content.json"
  scp -P "$DEPLOY_PORT" -o ControlPath="$MUX" \
    "$APPLY_PHP" "${DEPLOY_USER}@${DEPLOY_HOST}:/tmp/apply-wp-content.php"
  echo "Applying on production…"
  ssh -p "$DEPLOY_PORT" -o ControlPath="$MUX" "${DEPLOY_USER}@${DEPLOY_HOST}" \
    "trap 'rm -f /tmp/apply-wp-content.php /tmp/messcut-content.json' EXIT; WP_LOAD='${DEPLOY_PATH%/}/wp-load.php' php /tmp/apply-wp-content.php /tmp/messcut-content.json" \
    || die "content apply failed on production"
}

main() {
  local dry_run=0
  case "${1:-}" in
    -h|--help) usage; exit 0 ;;
    --dry-run) dry_run=1 ;;
    "") ;;
    *) die "unknown argument: $1 (try --help)" ;;
  esac

  trap 'rm -f "$PAYLOAD"' EXIT
  export_local
  if [[ "$dry_run" == 1 ]]; then
    echo "Dry run — nothing uploaded."
    exit 0
  fi
  load_env
  apply_remote
  echo "Content sync complete."
}

main "${1:-}"
