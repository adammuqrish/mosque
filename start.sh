#!/bin/bash
set -e

PORT=${PORT:-8080}

# Generate APP_KEY if not set (fixes MissingAppKeyException)
if ! php artisan key:generate --force --no-interaction -q 2>/dev/null; then
    echo "APP_KEY generated or already set."
fi

# Cache Laravel config for production
php artisan config:cache --no-interaction 2>/dev/null || true

php artisan serve --host=0.0.0.0 --port=$PORT
