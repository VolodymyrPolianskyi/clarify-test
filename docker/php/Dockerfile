FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer config audit.block-insecure false \
    && composer update --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html/var 2>/dev/null || true