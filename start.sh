#!/bin/bash

PORT=${PORT:-8080}

echo "=== Starting deployment setup v2 ==="

echo "--- Ensuring APP_KEY is set ---"
php artisan key:generate --no-interaction -q || echo "APP_KEY already set, skipping"

echo "--- Clearing all caches ---"
php artisan config:clear --no-interaction 2>&1 || true
php artisan cache:clear --no-interaction 2>&1 || true
php artisan view:clear --no-interaction 2>&1 || true
php artisan route:clear --no-interaction 2>&1 || true

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
DB_URL="${DATABASE_URL#mysql://}"
DB_USER="${DB_URL%%:*}"
DB_URL="${DB_URL#*:}"
DB_PASS="${DB_URL%%@*}"
DB_URL="${DB_URL#*@}"
DB_HOST="${DB_URL%%:*}"
DB_URL="${DB_URL#*:}"
DB_PORT="${DB_URL%%/*}"
DB_NAME="${DB_URL#*/}"

mysql --binary-mode --skip-ssl -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
  ALTER TABLE users ADD COLUMN is_amil tinyint(1) NOT NULL DEFAULT 0;
" 2>&1 && echo "Added is_amil column" || echo "is_amil: already exists or error"

mysql --binary-mode --skip-ssl -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
  CREATE TABLE IF NOT EXISTS settings (\`key\` varchar(255) NOT NULL PRIMARY KEY, value text NULL, created_at timestamp NULL, updated_at timestamp NULL);
" 2>&1 && echo "settings table ready" || echo "settings table error"

mysql --binary-mode --skip-ssl -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
  ALTER TABLE withdrawal_requests ADD COLUMN fund_purpose varchar(100) NULL;
" 2>&1 && echo "Added fund_purpose column" || echo "fund_purpose: already exists or error"

echo "--- Column check complete ---"

echo "--- Cleaning up legacy encrypted donor ICs ---"
php artisan donations:cleanup-donor-ic --no-interaction 2>&1 || true
echo "--- Cleanup complete ---"

echo "--- Running seeders ---"
php artisan db:seed --force --no-interaction 2>&1 || true
echo "--- Seeders complete ---"

echo "--- Caching config ---"
php artisan config:cache --no-interaction 2>&1 || true

echo "--- Creating storage link ---"
php artisan storage:link --no-interaction 2>&1 || true

echo "=== Starting PHP-FPM ==="
php-fpm -D 2>&1 || echo "PHP-FPM already running"

echo "=== Starting Nginx ==="
nginx -g "daemon off;" 2>&1
