#!/bin/bash
set -e

PORT=${PORT:-8080}

# Generate APP_KEY if not set
php artisan key:generate --force --no-interaction -q 2>/dev/null || echo "APP_KEY OK"

# Run migrations (safe to re-run, will skip already-run migrations)
php artisan migrate --force --no-interaction || echo "Migration issue — check logs above."

# Seed default users (admin@mosque.com / password) only if users table is empty
php artisan db:seed --force --no-interaction 2>/dev/null || true

# Cache config for production
php artisan config:cache --no-interaction 2>/dev/null || true

php artisan serve --host=0.0.0.0 --port=$PORT
