FROM php:8.1-fpm

ARG NODE_VERSION=20

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y libpq-dev libicu-dev curl ca-certificates zip unzip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pdo pdo_pgsql intl
