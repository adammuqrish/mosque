#!/bin/bash
PORT=${PORT:-8080}
php artisan serve --host=0.0.0.0 --port=$PORT
