FROM php:8.2-fpm

RUN apt-get update && apt-get install -y --no-install-recommends git unzip libzip-dev libpng-dev libonig-dev libxml2-dev zip curl nginx \
    && docker-php-ext-install pdo_mysql zip gd mbstring \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . .

RUN if [ ! -f .env.example ]; then echo "Error: .env.example not found!"; exit 1; fi && \
    cp -n .env.example .env && \
    composer install --no-dev --optimize-autoloader

# Copy files config và entrypoint
COPY nginx.conf /etc/nginx/sites-enabled/default
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

CMD ["entrypoint.sh"]
