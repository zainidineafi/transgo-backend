FROM composer:2.4.1 as build
WORKDIR /app
COPY . /app
RUN composer install

FROM php:8.1.10-apache
EXPOSE 80
COPY --from=build /app /app
COPY vhost.conf /etc/apache2/sites-available/000-default.conf

# Install MySQL client library
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set up environment variables for MySQL
ENV MYSQL_ROOT_PASSWORD=root
ENV MYSQL_DATABASE=transgo
ENV MYSQL_USER=user
ENV MYSQL_PASSWORD=password

# Ubah kepemilikan file/direktori
RUN chown -R www-data:www-data /app

# Aktifkan modul rewrite di dalam konteks Apache
RUN a2enmod rewrite