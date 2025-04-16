FROM php:7.4.33-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpq-dev \
    zip \
    unzip \
    vim \
    sudo \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get update && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN usermod -s /bin/bash www-data \
    && echo "www-data ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www
RUN mkdir -p storage/framework/sessions && chmod -R 777 storage && chown -R www-data:www-data storage
RUN git config --global --add safe.directory /var/www

EXPOSE 8000
