FROM php:7.3.6-fpm

WORKDIR /var/www/html

RUN docker-php-ext-install pdo_mysql

RUN apt-get update && \
    apt-get install -y git zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY ./composer.json /var/www/html

RUN composer install --no-dev

VOLUME /var/www/html/vendor/