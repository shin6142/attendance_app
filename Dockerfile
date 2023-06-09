FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    zip \
    unzip
RUN docker-php-ext-install pdo_mysql
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html