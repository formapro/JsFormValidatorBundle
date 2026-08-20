FROM php:8.5-fpm-bookworm
WORKDIR "/application"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        gnupg \
        libasound2 \
        libgbm1 \
        libgtk-3-0 \
        libgtk2.0-0 \
        libicu-dev \
        libnotify-dev \
        libnss3 \
        libsqlite3-dev \
        libxss1 \
        libxtst6 \
        libzip-dev \
        unzip \
        xauth \
        xvfb \
        zip; \
    docker-php-ext-install -j"$(nproc)" intl pdo_mysql pdo_sqlite zip; \
    mkdir -p /etc/apt/keyrings; \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
        | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg; \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_24.x nodistro main" \
        > /etc/apt/sources.list.d/nodesource.list; \
    apt-get update; \
    apt-get install -y --no-install-recommends nodejs; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
