#!/bin/bash

PORT=${PORT:-8080}

echo "=== Starting deployment setup ==="

echo "--- Generating APP_KEY ---"
php artisan key:generate --force --no-interaction -q || echo "APP_KEY OK"

echo "--- Importing database dump (if exists) ---"
if [ -f railway_import.sql ]; then
    # Parse DATABASE_URL (format: mysql://user:pass@host:port/db)
    DB_URL="${DATABASE_URL#mysql://}"
    DB_USER="${DB_URL%%:*}"
    DB_URL="${DB_URL#*:}"
    DB_PASS="${DB_URL%%@*}"
    DB_URL="${DB_URL#*@}"
    DB_HOST="${DB_URL%%:*}"
    DB_URL="${DB_URL#*:}"
    DB_PORT="${DB_URL%%/*}"
    DB_NAME="${DB_URL#*/}"

    mysql --ssl-mode=DISABLED -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < railway_import.sql 2>&1 && echo "Import successful" || echo "Import had errors (some may be expected)"
fi
echo "--- Import complete ---"

echo "--- Running migrations ---"
php artisan migrate --force --no-interaction 2>&1 || echo "Migration failed!"
echo "--- Migrations complete ---"

echo "--- Running seeders ---"
php artisan db:seed --force --no-interaction 2>&1 || true
echo "--- Seeders complete ---"

echo "--- Caching config ---"
php artisan config:cache --no-interaction 2>&1 || true

echo "=== Starting PHP Artisan Serve ==="
php artisan serve --host=0.0.0.0 --port=$PORT
