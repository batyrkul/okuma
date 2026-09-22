#!/usr/bin/env bash
set -euo pipefail

COMPOSE="docker compose"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "${SCRIPT_DIR}")"

cd "${PROJECT_ROOT}"

log()  { echo -e "\033[1;32m[deploy]\033[0m $*"; }
warn() { echo -e "\033[1;33m[deploy]\033[0m $*"; }
err()  { echo -e "\033[1;31m[deploy]\033[0m $*" >&2; exit 1; }

[[ -f ".env" ]] || err ".env not found. Run: cp .env.docker .env && edit it."
grep -q "^APP_KEY=base64:" .env || warn "APP_KEY appears empty – will be generated on startup."

log "Building Docker images..."
${COMPOSE} build --pull --no-cache

log "Pulling latest base images..."
${COMPOSE} pull --ignore-buildable

log "Starting services..."
${COMPOSE} up -d --remove-orphans --wait

log "Running post-deploy checks..."
${COMPOSE} exec app php artisan about --only=Environment,Cache,Database

log "Stack status:"
${COMPOSE} ps

log "✅ Deployment complete! Application is at: $(grep APP_URL .env | cut -d= -f2)"
