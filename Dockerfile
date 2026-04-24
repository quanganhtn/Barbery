FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    python3 \
    python3-pip \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        gd \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        bcmath \
        exif

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . /var/www

# Laravel
RUN composer install --no-dev --optimize-autoloader

# 🔥 FRONTEND (QUAN TRỌNG)
RUN rm -f public/hot
RUN npm install
RUN npm run build

# Python
RUN pip3 install --break-system-packages --no-cache-dir -r barbery_ai/requirements.txt

# Permission
RUN chmod +x /var/www/start.sh
RUN chmod -R 775 storage bootstrap/cache || true
RUN mkdir -p database && touch database/database.sqlite

EXPOSE 10000

CMD ["/var/www/start.sh"]
