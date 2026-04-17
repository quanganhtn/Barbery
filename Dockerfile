# Sử dụng image PHP chính thức từ Docker
FROM php:8.0-cli

# Cài đặt các thư viện cần thiết cho Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Cài Composer để quản lý các phụ thuộc PHP
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Cài đặt các phụ thuộc Laravel
WORKDIR /var/www
COPY . /var/www
RUN composer install

# Chạy lệnh PHP Artisan để khởi động ứng dụng Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
