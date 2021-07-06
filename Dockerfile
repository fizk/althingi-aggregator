FROM php:8.0.8-cli-buster

ARG ENV
ENV PATH="/var/www/html:/var/www/html/bin:${PATH}"

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
    mkdir -p /var/www/.composer; \
    chown www-data /var/www/.composer

RUN if [ "$ENV" != "production" ] ; then \
    pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.mode = debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.idekey=myKey" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
fi ;

WORKDIR /var/www/html
USER www-data

COPY --chown=www-data:www-data ./composer.json .
COPY --chown=www-data:www-data ./composer.lock .

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/var/www/html --filename=composer --version=2.1.3

RUN if [ "$ENV" != "production" ] ; then \
    composer install --prefer-source --no-interaction \
    && composer dump-autoload; \
    fi ;

RUN if [ "$ENV" = "production" ] ; then \
    composer install --prefer-source --no-interaction --no-dev -o \
    && composer dump-autoload -o; \
    fi ;

COPY --chown=www-data:www-data ./bin ./bin
COPY --chown=www-data:www-data ./config ./config
COPY --chown=www-data:www-data ./src ./src
COPY --chown=www-data:www-data ./public ./public

WORKDIR /var/www/html/bin
