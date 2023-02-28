FROM php:7.4-fpm

RUN pecl install xdebug \
   && docker-php-ext-enable xdebug


RUN apk add --update --no-cache \
    php8-pdo \
    php8-pdo_pgsql \
    php8-pgsql \

EXPOSE 9001