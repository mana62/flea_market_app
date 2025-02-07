FROM php:8.1-fpm

COPY php.ini /usr/local/etc/php/

RUN apt-get update \
  && apt-get install -y \
     default-mysql-client \
     zlib1g-dev \
     libzip-dev \
     unzip \
     libfreetype6-dev \
     libjpeg62-turbo-dev \
     libpng-dev \
     libwebp-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
  && docker-php-ext-install -j$(nproc) gd \
  && docker-php-ext-install pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer self-update

WORKDIR /var/www
