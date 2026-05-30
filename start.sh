#!/bin/bash

PORT=${PORT:-8080}

echo "=== Starting deployment setup ==="

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
