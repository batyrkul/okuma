#!/bin/bash
# =============================================================================
# Container entrypoint – runs once before supervisord takes over
# =============================================================================
set -euo pipefail

log() { echo "[entrypoint] $*"; }

# ── Wait for MySQL TCP port ────────────────────────────────────────────────────
# We check port reachability only — user creation (MYSQL_USER) happens inside
# MySQL init scripts that run after its own healthcheck passes.
# Laravel migrations below will surface any auth errors with a clear message.
if [[ -n "${DB_HOST:-}" ]]; then
    log "Waiting for MySQL TCP at ${DB_HOST}:${DB_PORT:-3306} ..."
    until bash -c "echo > /dev/tcp/${DB_HOST}/${DB_PORT:-3306}" 2>/dev/null; do
        sleep 2
    done
    # Extra grace period for MySQL user/database init scripts to complete
    sleep 3
    log "MySQL port is open."
fi

# ── Generate APP_KEY if missing ────────────────────────────────────────────────
if [[ -z "${APP_KEY:-}" ]]; then
    log "Generating APP_KEY..."
    php artisan key:generate --force
fi

# ── Storage link ──────────────────────────────────────────────────────────────
php artisan storage:link --force 2>/dev/null || true

# ── Run migrations ────────────────────────────────────────────────────────────
if [[ "${RUN_MIGRATIONS:-true}" == "true" ]]; then
    log "Running migrations..."
    php artisan migrate --force --no-interaction
fi

# ── Clear & warm caches (production) ─────────────────────────────────────────
if [[ "${APP_ENV:-production}" == "production" ]]; then
    log "Warming production caches..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
fi

log "Bootstrap complete. Handing off to supervisord."
exec "$@"
