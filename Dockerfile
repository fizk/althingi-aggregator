FROM php:7.2-cli

ENV LOG_PATH none

RUN apt-get update \
 && apt-get install -y zip unzip \
 && apt-get install -y git zlib1g-dev vim \
 && docker-php-ext-install zip \
 && curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

COPY . /usr/src

WORKDIR /usr/src

RUN /usr/local/bin/composer install --no-interaction  \
    && /usr/local/bin/composer dump-autoload -o
