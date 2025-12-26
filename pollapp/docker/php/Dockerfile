FROM php:8.3-fpm-alpine

# PDO + PostgreSQL драйвер
RUN apk add --no-cache postgresql-dev \
  && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html