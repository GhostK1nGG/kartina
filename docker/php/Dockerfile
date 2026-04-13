FROM php:8.3-fpm

ARG NODE_VERSION=22

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        unzip \
        zip \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
    && curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath exif gd intl pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY docker/php/conf.d/performance.ini /usr/local/etc/php/conf.d/zz-performance.ini
COPY docker/php/start-app /usr/local/bin/start-app
COPY docker/php/start-worker /usr/local/bin/start-worker
RUN chmod +x /usr/local/bin/start-app /usr/local/bin/start-worker

WORKDIR /var/www/html

COPY src/composer.json src/composer.lock ./
RUN composer install \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader
