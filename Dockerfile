FROM php:8.0.7-cli-buster

ARG ENV

RUN apt-get update; \
    apt-get install -y \
        zip \
        unzip \
        libzip-dev \
        zlib1g-dev \
        git \
        vim; \
    pecl install -o -f redis; \
    docker-php-ext-enable redis; \
    docker-php-ext-install zip; \
    rm -rf /tmp/pear; \
    curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer


RUN if [ "$ENV" != "production" ] ; then \
        pecl install xdebug; \
        docker-php-ext-enable xdebug; \
        echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "xdebug.mode = debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi ;

WORKDIR /usr/src

# USER www-data

COPY ./composer.json .
COPY ./composer.lock .

RUN if [ "$ENV" != "production" ] ; then \
    composer install --prefer-source --no-interaction \
    && composer dump-autoload; \
    fi ;

RUN if [ "$ENV" = "production" ] ; then \
    composer install --prefer-source --no-interaction --no-dev -o \
    && composer dump-autoload -o; \
    fi ;

COPY ./bin ./bin
COPY ./config ./config
COPY ./src ./src
COPY ./public ./public

WORKDIR /usr/src/bin
