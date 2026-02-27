FROM php:8.5-fpm

# Extensions systeme requises
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libfreetype-dev \
    libjpeg-dev \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        intl \
        gd \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Xdebug + APCu via PECL
RUN pecl install xdebug apcu \
    && docker-php-ext-enable xdebug apcu

# Configuration Xdebug (mode develop pour le dev local)
RUN echo "xdebug.mode=develop,debug" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/xdebug.ini

# Configuration APCu
RUN echo "apc.enable_cli=1" > /usr/local/etc/php/conf.d/apcu.ini

# Configuration PHP
RUN echo "upload_max_filesize = 10M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 10M" >> /usr/local/etc/php/conf.d/uploads.ini

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js 22 LTS
#RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
#    && apt-get install -y nodejs \
#    && apt-get clean \
#    && rm -rf /var/lib/apt/lists/*

RUN git config --global --add safe.directory /var/www/html

WORKDIR /var/www/html
