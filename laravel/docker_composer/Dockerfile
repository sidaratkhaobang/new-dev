FROM composer:2.2.7 as vendor

COPY database database
COPY composer.json composer.json
COPY composer.lock composer.lock

ARG COMPOSER_AUTH={}
ENV COMPOSER_AUTH=${COMPOSER_AUTH}


RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --no-dev
