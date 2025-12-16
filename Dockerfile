FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpq-dev \
    zip \
    curl \
    nginx \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_pgsql pgsql zip gd mbstring xml curl exif \
    && pecl install redis && docker-php-ext-enable redis \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy application files
COPY . .

# Create entrypoint script directly in Dockerfile to avoid line ending issues
RUN echo '#!/bin/bash' > /docker-entrypoint.sh && \
    echo 'set -e' >> /docker-entrypoint.sh && \
    echo 'echo "🚀 Starting Laravel application on PORT=${PORT:-8080}..."' >> /docker-entrypoint.sh && \
    echo 'echo "Environment: APP_ENV=${APP_ENV}, APP_DEBUG=${APP_DEBUG}"' >> /docker-entrypoint.sh && \
    echo '' >> /docker-entrypoint.sh && \
    echo '# Check APP_KEY' >> /docker-entrypoint.sh && \
    echo 'if [ -z "$APP_KEY" ] || [ "${#APP_KEY}" -lt 20 ]; then' >> /docker-entrypoint.sh && \
    echo '  echo "❌ ERROR: APP_KEY is not set or invalid!"' >> /docker-entrypoint.sh && \
    echo '  echo "Please set APP_KEY in Render Environment Variables"' >> /docker-entrypoint.sh && \
    echo '  echo "Generate one with: bash generate-app-key.sh"' >> /docker-entrypoint.sh && \
    echo '  echo "Example: APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"' >> /docker-entrypoint.sh && \
    echo 'else' >> /docker-entrypoint.sh && \
    echo '  echo "✅ APP_KEY is set (length: ${#APP_KEY})"' >> /docker-entrypoint.sh && \
    echo 'fi' >> /docker-entrypoint.sh && \
    echo '' >> /docker-entrypoint.sh && \
    echo '# Set permissions' >> /docker-entrypoint.sh && \
    echo 'echo "🔒 Setting permissions..."' >> /docker-entrypoint.sh && \
    echo 'chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true' >> /docker-entrypoint.sh && \
    echo 'chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true' >> /docker-entrypoint.sh && \
    echo '' >> /docker-entrypoint.sh && \
    echo '# Clear and optimize (without database)' >> /docker-entrypoint.sh && \
    echo 'echo "🔧 Optimizing Laravel..."' >> /docker-entrypoint.sh && \
    echo 'php artisan config:clear || true' >> /docker-entrypoint.sh && \
    echo 'php artisan route:clear || true' >> /docker-entrypoint.sh && \
    echo 'php artisan view:clear || true' >> /docker-entrypoint.sh && \
    echo '' >> /docker-entrypoint.sh && \
    echo '# Check if database is configured' >> /docker-entrypoint.sh && \
    echo 'if [ ! -z "$DB_HOST" ] && [ "$DB_HOST" != "mysql" ] && [ "$DB_HOST" != "127.0.0.1" ] && [ "$DB_HOST" != "localhost" ]; then' >> /docker-entrypoint.sh && \
    echo '  echo "⏳ Database configured ($DB_HOST), waiting for connection..."' >> /docker-entrypoint.sh && \
    echo '  RETRY=0' >> /docker-entrypoint.sh && \
    echo '  MAX_RETRY=30' >> /docker-entrypoint.sh && \
    echo '  until php artisan db:show 2>/dev/null || [ $RETRY -eq $MAX_RETRY ]; do' >> /docker-entrypoint.sh && \
    echo '    echo "  Waiting for database... ($RETRY/$MAX_RETRY)"' >> /docker-entrypoint.sh && \
    echo '    RETRY=$((RETRY+1))' >> /docker-entrypoint.sh && \
    echo '    sleep 2' >> /docker-entrypoint.sh && \
    echo '  done' >> /docker-entrypoint.sh && \
    echo '  if [ $RETRY -eq $MAX_RETRY ]; then' >> /docker-entrypoint.sh && \
    echo '    echo "⚠️ Database connection timeout, starting without migration"' >> /docker-entrypoint.sh && \
    echo '  else' >> /docker-entrypoint.sh && \
    echo '    echo "✅ Database connected! Running migrations..."' >> /docker-entrypoint.sh && \
    echo '    php artisan migrate --force || echo "Migration failed, continuing..."' >> /docker-entrypoint.sh && \
    echo '  fi' >> /docker-entrypoint.sh && \
    echo 'else' >> /docker-entrypoint.sh && \
    echo '  echo "⚠️ No database configured (DB_HOST not set)"' >> /docker-entrypoint.sh && \
    echo 'fi' >> /docker-entrypoint.sh && \
    echo '' >> /docker-entrypoint.sh && \
    echo '# Start PHP-FPM in background' >> /docker-entrypoint.sh && \
    echo 'echo "🚀 Starting PHP-FPM..."' >> /docker-entrypoint.sh && \
    echo 'php-fpm -D' >> /docker-entrypoint.sh && \
    echo '' >> /docker-entrypoint.sh && \
    echo '# Configure Nginx port' >> /docker-entrypoint.sh && \
    echo 'PORT=${PORT:-8080}' >> /docker-entrypoint.sh && \
    echo 'echo "🌐 Configuring Nginx to listen on port $PORT"' >> /docker-entrypoint.sh && \
    echo 'sed -i "s/listen 80 default_server;/listen $PORT default_server;/g" /etc/nginx/sites-available/default' >> /docker-entrypoint.sh && \
    echo 'sed -i "s/listen \[::\]:80 default_server;/listen [::]:$PORT default_server;/g" /etc/nginx/sites-available/default' >> /docker-entrypoint.sh && \
    echo '' >> /docker-entrypoint.sh && \
    echo '# Test nginx config' >> /docker-entrypoint.sh && \
    echo 'echo "🔍 Testing Nginx configuration..."' >> /docker-entrypoint.sh && \
    echo 'nginx -t 2>&1' >> /docker-entrypoint.sh && \
    echo '' >> /docker-entrypoint.sh && \
    echo '# Test Laravel is responding' >> /docker-entrypoint.sh && \
    echo 'echo "🧪 Testing Laravel health..."' >> /docker-entrypoint.sh && \
    echo 'sleep 1' >> /docker-entrypoint.sh && \
    echo 'if curl -s http://127.0.0.1:$PORT/health > /dev/null 2>&1; then' >> /docker-entrypoint.sh && \
    echo '  echo "✅ Laravel is responding!"' >> /docker-entrypoint.sh && \
    echo 'else' >> /docker-entrypoint.sh && \
    echo '  echo "⚠️ Warning: Laravel health check failed"' >> /docker-entrypoint.sh && \
    echo '  echo "Check storage permissions and APP_KEY"' >> /docker-entrypoint.sh && \
    echo 'fi' >> /docker-entrypoint.sh && \
    echo '' >> /docker-entrypoint.sh && \
    echo 'echo "✅ Starting Nginx on 0.0.0.0:$PORT"' >> /docker-entrypoint.sh && \
    echo 'echo "Ready to accept connections!"' >> /docker-entrypoint.sh && \
    echo 'exec nginx -g "daemon off;"' >> /docker-entrypoint.sh && \
    chmod +x /docker-entrypoint.sh

# Setup Laravel application
RUN if [ -f .env.example ]; then \
        cp .env.example .env; \
    else \
        echo "APP_NAME=\"Modern Web Shop\"" > .env && \
        echo "APP_ENV=production" >> .env && \
        echo "APP_KEY=" >> .env && \
        echo "APP_DEBUG=false" >> .env && \
        echo "APP_URL=http://localhost" >> .env; \
    fi && \
    # Remove DB settings from .env - will be set via environment variables only \
    sed -i '/^DB_/d' .env && \
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist && \
    php artisan key:generate --force && \
    mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY nginx.conf /etc/nginx/sites-available/default
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE ${PORT:-8080}

ENTRYPOINT ["/docker-entrypoint.sh"]
