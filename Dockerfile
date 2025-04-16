FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
    git unzip curl zip vim \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libzip-dev libonig-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql pdo_pgsql mbstring zip exif pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

WORKDIR /var/www

COPY . /var/www

RUN mkdir -p \
    /var/www/storage/framework/cache/data \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/storage/logs \
    /var/www/bootstrap/cache

RUN chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www && \
    chmod -R 777 /var/www/storage && \
    chmod -R 777 /var/www/bootstrap/cache

USER www-data
RUN composer install --no-dev --optimize-autoloader --no-interaction
USER root

COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf
RUN apache2ctl configtest

EXPOSE 80

CMD ["apache2-foreground"]