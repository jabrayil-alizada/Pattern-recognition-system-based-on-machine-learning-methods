FROM php:7.4.1-fpm-alpine

RUN apk --no-cache add git postgresql-dev postgresql-client libzip-dev zip libpng-dev \
    && mkdir -p /usr/src/php/ext/redis \
    && docker-php-ext-install pdo pdo_pgsql;

COPY --from=composer:1.9.0 /usr/bin/composer /usr/bin/composer