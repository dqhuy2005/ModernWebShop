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
    zip \
    curl \
    nginx \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd mbstring xml curl exif \
    && pecl install redis && docker-php-ext-enable redis \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy application files
COPY . .

# Create entrypoint script directly in Dockerfile to avoid line ending issues
RUN echo '#!/bin/bash' > /docker-entrypoint.sh && \
    echo 'set -e' >> /docker-entrypoint.sh && \
    echo 'echo "🚀 Starting Laravel application..."' >> /docker-entrypoint.sh && \
    echo 'until php artisan db:show 2>/dev/null; do echo "Waiting for database..."; sleep 2; done' >> /docker-entrypoint.sh && \
    echo 'php artisan migrate --force' >> /docker-entrypoint.sh && \
    echo 'php artisan config:cache' >> /docker-entrypoint.sh && \
    echo 'chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache' >> /docker-entrypoint.sh && \
    echo 'chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache' >> /docker-entrypoint.sh && \
    echo 'php-fpm -D' >> /docker-entrypoint.sh && \
    echo 'PORT=${PORT:-8080}' >> /docker-entrypoint.sh && \
    echo 'sed -i "s/listen 80;/listen $PORT;/g" /etc/nginx/sites-available/default' >> /docker-entrypoint.sh && \
    echo 'nginx -g "daemon off;"' >> /docker-entrypoint.sh && \
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
    composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist && \
    php artisan key:generate --force && \
    mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY nginx.conf /etc/nginx/sites-available/default
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE ${PORT:-8080}

ENTRYPOINT ["/docker-entrypoint.sh"]
