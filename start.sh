#!/bin/bash
set -e

echo "==> Running database migrations..."
php artisan migrate --force

echo "==> Linking storage..."
php artisan storage:link 2>/dev/null || true

echo "==> Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Starting Laravel on port $PORT..."
php artisan serve --host=0.0.0.0 --port="$PORT"
