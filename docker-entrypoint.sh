#!/bin/bash
set -e

echo "ðŸš€ Starting Laravel application setup..."

# Wait for database to be ready
echo "â³ Waiting for database connection..."
until php artisan db:show 2>/dev/null; do
    echo "   Database is unavailable - sleeping"
    sleep 2
done

echo "âœ… Database is ready!"

# Run migrations
echo "ðŸ“¦ Running database migrations..."
php artisan migrate --force

# Clear and cache config
echo "ðŸ”§ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
echo "ðŸ”’ Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "âœ¨ Laravel application is ready!"

# Start PHP-FPM
php-fpm -D

# Configure Nginx port dynamically
PORT=${PORT:-8080}
sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/sites-available/default

# Start Nginx
echo "ðŸŒ Starting web server on port $PORT..."
nginx -g "daemon off;"
