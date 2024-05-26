FROM php:8.1-fpm

# Update package list dan install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libxml2-dev \
    libxslt1-dev \
    libicu-dev \
    libonig-dev \
    g++

# Membersihkan cache apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Instal Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Mengatur direktori kerja
WORKDIR /var/www

# Menetapkan izin untuk direktori Laravel
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache

# Menyalin file composer.lock dan composer.json, dan menginstal dependensi PHP
COPY composer.lock composer.json /var/www/
RUN composer install

# Mengekspos port 9000 dan menjalankan server php-fpm
EXPOSE 9000
CMD ["php-fpm"]
