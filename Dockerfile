FROM php:8.1.2-cli-buster

ARG ENV
ENV PATH="/var/app/bin:${PATH}"

# Sets up the directory that the app will be running
# in and the user `agent` that will be running the app
RUN mkdir -p /var/app; \
    useradd -ms /bin/bash agent; \
    chown agent:agent /var/app;

# Configures the host system and install dependencies
# and extensions.
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
    mkdir -p /var/app/.composer; \
    chown agent /var/app/.composer

# Instal Xdebug if the the container if in development
# (not in production) mode. It also sets up some aliases,
# like `ll`, `phpunit` and `cover`
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
    echo "alias ll='ls -al'\n" >> /home/agent/.bashrc; \
    echo "alias phpunit='/var/app/vendor/bin/phpunit'\n" >> /home/agent/.bashrc; \
    echo 'alias cover="XDEBUG_MODE=coverage /var/app/vendor/bin/phpunit --coverage-html=\"/var/app/tests/docs\""\n' >> /home/agent/.bashrc; \
fi ;

# Sets the current working directory and sent the user
# to the user who will be running the app (`agent`)
WORKDIR /var/app
USER agent

# Copies Composer related files into the container as
# `agent`. Installs Composer and then installs Composer
# dependencies as required by the mode (production/development)
COPY --chown=agent:agent ./composer.json .
COPY --chown=agent:agent ./composer.lock .

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/var/app --filename=composer --version=2.4.1

RUN if [ "$ENV" != "production" ] ; then \
    ./composer install --prefer-source --no-interaction \
    && ./composer dump-autoload; \
    fi ;

RUN if [ "$ENV" = "production" ] ; then \
    ./composer install --prefer-source --no-interaction --no-dev -o \
    && ./composer dump-autoload -o; \
    fi ;

# Copies source-code and configuration into the container
# as `agent`.
COPY --chown=agent:agent ./bin ./bin
COPY --chown=agent:agent ./config ./config
COPY --chown=agent:agent ./src ./src
COPY --chown=agent:agent ./public ./public

# WORKDIR /var/www/html/bin
