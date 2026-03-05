FROM php:8.4-cli-alpine

WORKDIR /app

RUN apk add git

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql pgsql gd zip intl bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk add --update npm