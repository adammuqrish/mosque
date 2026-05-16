#!/bin/bash

PORT=${PORT:-8080}

echo "=== Starting deployment setup ==="

echo "--- Generating APP_KEY ---"
php artisan key:generate --force --no-interaction -q || echo "APP_KEY OK"

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
