FROM composer:2.4.1 as build
WORKDIR /app
COPY . /app
RUN composer install

FROM php:8.1.10-apache
EXPOSE 80
COPY --from=build /app /app
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
RUN chown -R www-data:www-data /app && \
    a2enmod rewrite && \
    apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_mysql && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*


