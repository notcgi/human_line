# base stage for all over versions
FROM php:8.0-fpm-alpine3.13 AS base
WORKDIR "/app"
# available extensions: https://github.com/mlocati/docker-php-extension-installer#supported-php-extensions
COPY --from=mlocati/php-extension-installer:1.2.28 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql intl gd redis openssl
RUN echo "access.log = /dev/null" >> /usr/local/etc/php-fpm.d/www.conf
RUN echo "[global]" >> /usr/local/etc/php-fpm.d/www.conf
RUN echo "log_limit = 65536" >> /usr/local/etc/php-fpm.d/www.conf

# version for local development with composer and xdebug. You can run composer install | update inside container
FROM base AS dev
RUN install-php-extensions xdebug
COPY --from=composer:2.1.3 /usr/bin/composer /usr/bin/composer

# builder stage
FROM base AS builder
COPY --from=composer:2.1.3 /usr/bin/composer /usr/bin/composer
VOLUME composer-cache:/tmp/
COPY composer.json composer.lock /app/
RUN composer install  \
# далее строка костыль, которую надо убрать, когда либа с jwt начнет поддерживать php8
    --ignore-platform-req=php \
    --no-ansi \
    --no-autoloader \
    --no-interaction \
    --no-scripts
COPY . /app
RUN composer dump-autoload --optimize --classmap-authoritative

# version for stage and production environment with build in application code
FROM base AS common
COPY --from=builder /app /app
