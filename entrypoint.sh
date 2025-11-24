#!/bin/bash

# Generate key (nếu APP_KEY chưa set, Laravel tự handle, nhưng chạy explicit cho chắc)
php artisan key:generate --quiet || true  # --quiet để im lặng nếu fail nhẹ, || true tránh crash

# Setup storage folders & permissions (thực tế, Laravel cần writable bởi www-data)
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Nếu cần migrate DB hoặc cache config (thực tế production hay add):
php artisan migrate --force
php artisan config:cache
php artisan route:cache

# Run server
exec php-fpm -D && nginx -g 'daemon off;'
