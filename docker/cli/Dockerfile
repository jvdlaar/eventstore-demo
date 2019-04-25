FROM php:7.2.11-cli-alpine3.7 AS php-cli

RUN apk add --no-cache \
    postgresql-dev

RUN docker-php-ext-install \
    opcache \
    pcntl \
    posix \
    sockets \
    pdo \
    pdo_pgsql

RUN apk add --virtual .build-dependencies --no-cache \
    autoconf \
    gcc \
    git \
    libc-dev \
    make \
    && pecl channel-update pecl.php.net \
    # compile
    && git clone https://github.com/allegro/php-protobuf \
    && cd php-protobuf \
    && git checkout v0.12.3 \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && docker-php-ext-enable protobuf \
    # cleanup
    && rm -rf ../php-protobuf \
    && apk del .build-dependencies

WORKDIR /app

FROM php-cli AS composer

RUN apk add --no-cache \
    curl \
    git

ENV COMPOSER_MEMORY_LIMIT -1
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer

COPY --from=composer:1.7.2 /usr/bin/composer /usr/bin/composer

RUN composer global require hirak/prestissimo
