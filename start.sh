#!/bin/bash

PORT=${PORT:-8080}

echo "=== Starting deployment setup v3 ==="

echo "--- Ensuring APP_KEY is set ---"
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --no-interaction -q || echo "key:generate failed (runtime key may already be set via env)"
else
    echo "APP_KEY already provided via environment, skipping key:generate"
fi

echo "--- Clearing all caches ---"
php artisan config:clear --no-interaction 2>&1 || true
php artisan cache:clear --no-interaction 2>&1 || true
php artisan view:clear --no-interaction 2>&1 || true
php artisan route:clear --no-interaction 2>&1 || true

# Build mysql connection args from DATABASE_URL (Railway-style) or DB_* env (droplet-style)
if [ -n "$DATABASE_URL" ]; then
    DB_URL="${DATABASE_URL#mysql://}"
    DB_USER="${DB_URL%%:*}"
    DB_URL="${DB_URL#*:}"
    DB_PASS="${DB_URL%%@*}"
    DB_URL="${DB_URL#*@}"
    DB_HOST="${DB_URL%%:*}"
    DB_URL="${DB_URL#*:}"
    DB_PORT="${DB_URL%%/*}"
    DB_NAME="${DB_URL#*/}"
else
    DB_HOST="${DB_HOST:-127.0.0.1}"
    DB_PORT="${DB_PORT:-3306}"
    DB_NAME="${DB_DATABASE:-mosque}"
    DB_USER="${DB_USERNAME:-root}"
    DB_PASS="${DB_PASSWORD:-}"
fi

MYSQL_ARGS=(--binary-mode --skip-ssl -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" "$DB_NAME")
if [ -n "$DB_PASS" ]; then
    MYSQL_ARGS+=("-p$DB_PASS")
fi

echo "--- Checking migration status ---"
php artisan migrate:status --no-interaction 2>&1

echo "--- Running migrations ---"
php artisan migrate --force --no-interaction 2>&1
MIGRATE_EXIT=$?
if [ $MIGRATE_EXIT -ne 0 ]; then
    echo "ERROR: Migration failed with exit code $MIGRATE_EXIT"
fi
echo "--- Migrations complete ---"

echo "--- Ensuring critical columns exist via raw SQL ---"
mysql "${MYSQL_ARGS[@]}" -e "
  ALTER TABLE users ADD COLUMN is_amil tinyint(1) NOT NULL DEFAULT 0;
" 2>&1 && echo "Added is_amil column" || echo "is_amil: already exists or error"

mysql "${MYSQL_ARGS[@]}" -e "
  CREATE TABLE IF NOT EXISTS settings (\`key\` varchar(255) NOT NULL PRIMARY KEY, value text NULL, created_at timestamp NULL, updated_at timestamp NULL);
" 2>&1 && echo "settings table ready" || echo "settings table error"

mysql "${MYSQL_ARGS[@]}" -e "
  ALTER TABLE withdrawal_requests ADD COLUMN fund_purpose varchar(100) NULL;
" 2>&1 && echo "Added fund_purpose column" || echo "fund_purpose: already exists or error"

echo "--- Column check complete ---"

echo "--- Cleaning up legacy encrypted donor ICs ---"
php artisan donations:cleanup-donor-ic --no-interaction 2>&1 || true
echo "--- Cleanup complete ---"

echo "--- Checking if database needs seeding ---"
USER_COUNT=$(mysql "${MYSQL_ARGS[@]}" -N -e "SELECT COUNT(*) FROM users;" 2>/dev/null | tr -d '[:space:]')
if [ -z "$USER_COUNT" ] || [ "$USER_COUNT" = "0" ]; then
    echo "Database is empty — running seeders"
    php artisan db:seed --force --no-interaction 2>&1 || true
else
    echo "Database already has $USER_COUNT user(s) — skipping seeders"
fi
echo "--- Seeders step complete ---"

echo "--- Caching config ---"
php artisan config:cache --no-interaction 2>&1 || true

echo "--- Creating storage link ---"
php artisan storage:link --no-interaction 2>&1 || true

echo "=== Starting PHP-FPM ==="
php-fpm -D 2>&1 || echo "PHP-FPM already running"

echo "=== Starting Nginx (daemonized) ==="
nginx 2>&1 || echo "Nginx already running"

echo "=== Starting Laravel Scheduler (foreground) ==="
php artisan schedule:work 2>&1
