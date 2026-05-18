#!/bin/bash
chmod -R 775 storage bootstrap/cache
php artisan migrate --force
php artisan config:clear
php artisan route:clear
php artisan view:clear
