FROM php:7.2-cli

ENV LOG_PATH none
ARG WITH_XDEBUG
ENV ENV_WITH_XDEBUG=$WITH_XDEBUG

RUN apt-get update \
 && apt-get install -y zip unzip \
 && apt-get install -y git zlib1g-dev vim \
 && docker-php-ext-install zip \
 && curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

RUN if [ $ENV_WITH_XDEBUG = "true" ] ; then \
        pecl install xdebug; \
        docker-php-ext-enable xdebug; \
        echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi ;

WORKDIR /usr/src

COPY ./composer.json .
COPY ./composer.lock .
COPY ./phpcs.xml .
COPY ./phpunit.xml.dist .

# COPY ./auto/ .    ## This is what docker-compose does
# COPY ./config/ .
# COPY ./module/ .
# COPY ./public/ .

RUN mkdir -p /usr/src/data/cache

RUN /usr/local/bin/composer install --no-interaction  \
    && /usr/local/bin/composer dump-autoload -o
