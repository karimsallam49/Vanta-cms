# VANTA MOTO CMS — all-in-one container (Laravel 12 + SQLite)
FROM php:8.2-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        libsqlite3-dev libzip-dev libpng-dev libicu-dev libonig-dev \
        zip unzip git \
    && docker-php-ext-install pdo_sqlite zip gd intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && mkdir -p database \
    && touch database/database.sqlite \
    && cp -n .env.example .env \
    && php artisan storage:link || true

# php -S worker pool — serve is a dev server, give it parallelism
ENV PHP_CLI_SERVER_WORKERS=8
EXPOSE 8080

# Fresh seed on every boot → deterministic demo data for reviewers.
# APP_KEY falls back to a generated one if the platform didn't set it.
CMD [ -z "$APP_KEY" ] && php artisan key:generate --force; \
    php artisan migrate:fresh --seed --force \
    && php artisan config:cache \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
