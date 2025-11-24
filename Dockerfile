FROM php:8.2-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev zip curl nginx \
    && docker-php-ext-install pdo_mysql zip gd mbstring \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
COPY . .

RUN if [ ! -f .env.example ]; then echo "Error: .env.example not found!"; exit 1; fi
RUN cp -n .env.example .env

RUN composer install --no-dev --optimize-autoloader

RUN php artisan key:generate

RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY nginx.conf /etc/nginx/sites-enabled/default

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
