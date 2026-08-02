#!/bin/sh
set -e

echo "==> Running Laravel Migrations..."
php artisan migrate --force

echo "==> Running Database Seeders..."
php artisan db:seed --force || true

echo "==> Clearing and Caching Config..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Creating Storage Link..."
php artisan storage:link || true

echo "==> Starting Laravel Web Server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
