# Two images from one file:
#   --target app  PHP-FPM with the Laravel code
#   --target web  nginx with the site config and the public/ files
#
# The app image is built in two stages. "build" has compilers, -dev headers and
# Composer; "app" gets only the compiled PHP extensions, their runtime libraries
# and the finished code. Build tools never reach production.

# ---------- build: compile PHP extensions and install Composer dependencies ----------
FROM php:8.3-fpm AS build

RUN apt-get update && apt-get install -y --no-install-recommends \
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

RUN composer dump-autoload --no-dev --optimize


# ---------- app: runtime only ----------
FROM php:8.3-fpm AS app

# Runtime libraries for the extensions (the -dev packages above minus headers),
# then remove the compilers the base image ships with ($PHPIZE_DEPS: gcc, make,
# libc-dev, ...). "libc-dev" is a virtual name, so libc6-dev and the kernel
# headers it pulls in are named explicitly. Installing the libraries first marks
# them as wanted, so --auto-remove leaves them in place.
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq5 \
    libzip5 \
    libpng16-16t64 \
    libjpeg62-turbo \
    libfreetype6 \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS libc6-dev linux-libc-dev libcrypt-dev rpcsvc-proto \
    && rm -rf /var/lib/apt/lists/*

# Compiled extensions and the ini files that enable them
COPY --from=build /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=build /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

WORKDIR /var/www/html

COPY --from=build /var/www/html /var/www/html

# Only the folders Laravel writes to belong to the PHP-FPM worker user
RUN chown -R www-data:www-data storage bootstrap/cache


FROM nginx:stable-alpine AS web

ENV PHP_FPM_HOST=app
COPY docker/nginx/default.conf.template /etc/nginx/templates/default.conf.template
COPY public /var/www/html/public
