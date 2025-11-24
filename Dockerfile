FROM php:8.2-fpm

RUN apt-get update && apt-get install -y git unzip libzip-dev libpng-dev libonig-dev libxml2-dev zip curl \
    && docker-php-ext-install pdo_mysql zip gd mbstring \
    && pecl install redis && docker-php-ext-enable redis \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .

RUN if [ ! -f .env.example ]; then echo "Error: .env.example not found!"; exit 1; fi && \
    cp .env.example .env && \
    composer install --no-dev --optimize-autoloader && \
    php artisan key:generate && \
    mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

EXPOSE $PORT

CMD php artisan config:clear && php artisan serve --host=0.0.0.0 --port=$PORT
