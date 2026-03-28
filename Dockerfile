FROM php:8.3-cli

RUN --mount=type=bind,from=ghcr.io/php/pie:bin,source=/pie,target=/usr/local/bin/pie \
    export DEBIAN_FRONTEND="noninteractive"; \
    set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libpq-dev \
        unzip \
        autoconf \
        gcc \
        make; \
    pie install --no-cache xdebug/xdebug; \
    docker-php-ext-install pdo pdo_pgsql; \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
        autoconf gcc make; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

CMD ["php", "-a"]
