FROM php:8.3.8-cli-bookworm

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
    pecl channel-update pecl.php.net;

RUN pecl install -o -f redis-6.0.2; \
    docker-php-ext-enable redis; \
    docker-php-ext-install zip; \
    rm -rf /tmp/pear; \
    mkdir -p /var/app/.composer; \
    chown agent /var/app/.composer

# Instal Xdebug if the the container if in development
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

# Sets the current working directory
WORKDIR /var/app

# Copies Composer related files into the container as
# `agent`. Installs Composer and then installs Composer
# dependencies as required by the mode (production/development)
COPY --chown=agent:agent ./composer.json .
COPY --chown=agent:agent ./composer.lock .

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer --version=2.7.6

# Set the user
# to the user who will be running the app (`agent`)
USER agent

RUN if [ "$ENV" != "production" ] ; then \
    composer install --no-interaction --ignore-platform-reqs \
    && composer dump-autoload; \
    fi ;

RUN if [ "$ENV" = "production" ] ; then \
    composer install --no-interaction --no-dev -o \
    && composer dump-autoload -o; \
    fi ;

# Copies source-code and configuration into the container
# as `agent`.
COPY --chown=agent:agent ./bin ./bin
COPY --chown=agent:agent ./config ./config
COPY --chown=agent:agent ./src ./src
COPY --chown=agent:agent ./public ./public

# WORKDIR /var/app/bin
