FROM composer:2.8 AS composer-deps

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --no-scripts \
    --prefer-dist \
    --ignore-platform-reqs

COPY . .

RUN composer dump-autoload \
    --optimize \
    --no-dev \
    --no-scripts \
    --classmap-authoritative

FROM node:22-alpine AS node-build

WORKDIR /app

RUN apk add --no-cache \
    php83 \
    php83-cli \
    php83-phar \
    php83-mbstring \
    php83-openssl \
    php83-tokenizer \
    php83-xml \
    php83-xmlwriter \
    php83-simplexml \
    php83-dom \
    php83-fileinfo \
    php83-ctype \
    php83-curl \
    php83-iconv \
    php83-intl \
    php83-bcmath \
    php83-pdo \
    php83-pdo_sqlite \
    php83-sqlite3 \
    php83-session \
    php83-posix \
  && ln -sf /usr/bin/php83 /usr/bin/php

COPY package.json package-lock.json* pnpm-workspace.yaml* ./
RUN npm ci --ignore-scripts

COPY . .
COPY --from=composer-deps /app/vendor ./vendor

RUN cp .env.example .env \
    && sed -i 's|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|' .env \
    && touch database/database.sqlite \
    && php artisan key:generate --force

RUN php artisan package:discover --ansi

RUN php artisan migrate --force --no-interaction

RUN php artisan wayfinder:generate --with-form

RUN npm run build

FROM php:8.3-fpm-alpine AS runtime

LABEL maintainer="okuma-team"
LABEL description="Okuma Laravel application – PHP-FPM runtime"

RUN apk add --no-cache \
    bash \
    curl \
    shadow \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    mysql-client \
    supervisor \
    oniguruma-dev \
    libzip-dev \
    icu-dev \
    linux-headers \
    fcgi

RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        intl \
        mbstring \
        opcache \
        pdo \
        pdo_mysql \
        pcntl \
        zip

COPY docker/php/conf/php.ini /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/conf/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/conf/opcache.ini /usr/local/etc/php/conf.d/98-opcache.ini

COPY docker/php/conf/supervisord.conf /etc/supervisord.conf

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=composer-deps /app/vendor ./vendor
COPY --chown=www-data:www-data --from=node-build   /app/public/build ./public/build

RUN mkdir -p storage/framework/{cache,sessions,views} \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD SCRIPT_NAME=/ping SCRIPT_FILENAME=/ping REQUEST_METHOD=GET \
        cgi-fcgi -bind -connect 127.0.0.1:9000 || exit 1

EXPOSE 9000

USER www-data

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
