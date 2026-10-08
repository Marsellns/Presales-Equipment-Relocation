FROM php:8.2-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip libcurl4-openssl-dev libfreetype6-dev libicu-dev \
        libjpeg62-turbo-dev libonig-dev libpng-dev libsqlite3-dev \
        libxml2-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath curl dom gd intl mbstring pdo_mysql pdo_sqlite xml zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.8 /usr/bin/composer /usr/local/bin/composer
WORKDIR /app
ENV APP_ENV=testing \
    DB_CONNECTION=sqlite DB_DATABASE=:memory: CACHE_STORE=array SESSION_DRIVER=array

COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-interaction --no-progress --no-scripts

COPY . .
RUN touch .env \
    && composer dump-autoload --optimize --no-interaction \
    && composer check-platform-reqs \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan optimize:clear

CMD ["php", "artisan", "test"]
