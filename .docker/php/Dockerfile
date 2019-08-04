# PHP Base and Dev
FROM php:7.3.6-fpm AS php-base

WORKDIR /var/www/html

RUN docker-php-ext-install pdo_mysql

# PHP Production
FROM php-base AS php-prod

RUN apt-get update && \
    apt-get install -y git zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY ./composer.json /var/www/html

RUN composer install --no-dev

VOLUME /var/www/html/vendor/

# PHP Development
FROM php-base AS php-dev