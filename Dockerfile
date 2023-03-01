FROM php:8.1-fpm as php

RUN usermod -u 1000 www-data
RUN apt-get update -y
RUN apt-get install -y unzip libpq-dev libcurl4-gnutls-dev nginx
RUN docker-php-ext-install pdo pdo_mysql bcmath curl opcache

# RUN pecl install -o -f redis \
#     && rm -rf /tmp/per \
#     && docker-php-ext-enable redis

WORKDIR /var/www
COPY --chown=www-data . .


COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer

RUN php artisan cache:clear
RUN php artisan config:clear

RUN chmod -R 755 /var/www/storage
RUN chmod -R 755 /var/www/bootstrap

ENTRYPOINT ["docker/entrypoint.sh"]
