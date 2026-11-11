FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

COPY public/ /var/www/html/
COPY assets/ /var/www/assets/
COPY src/ /var/www/src/
COPY .htaccess /var/www/html/.htaccess

RUN mkdir -p /var/www/storage/sessions \
    && chown -R www-data:www-data /var/www/html /var/www/src /var/www/storage
