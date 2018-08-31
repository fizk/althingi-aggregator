FROM php:7.2-cli

RUN apt-get update \
 && apt-get install -y  vim

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

ADD ./ /usr/src

WORKDIR /usr/src/public
