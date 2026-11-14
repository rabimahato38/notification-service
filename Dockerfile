# Two images from one file:
#   --target app  PHP-FPM with the Laravel code
#   --target web  nginx with the site config and the public/ files

FROM php:8.3-fpm AS app

RUN apt-get update && apt-get install -y \
    unzip \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        bcmath \
        gd \
        zip \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dependencies first: this layer is reused until composer.json or composer.lock change
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --no-interaction \
    --prefer-dist

COPY . .

RUN composer dump-autoload --no-dev --optimize \
    && chown -R www-data:www-data storage bootstrap/cache


FROM nginx:stable-alpine AS web

ENV PHP_FPM_HOST=app
COPY docker/nginx/default.conf.template /etc/nginx/templates/default.conf.template
COPY public /var/www/html/public
