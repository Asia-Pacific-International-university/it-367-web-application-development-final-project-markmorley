FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libpq-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --optimize-autoloader

COPY . .
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi

ENV PHP_CLI_SERVER_WORKERS=4

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --class=ProductionSeeder --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]