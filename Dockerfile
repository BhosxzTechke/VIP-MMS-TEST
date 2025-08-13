FROM php:8.2-fpm

# OS deps
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev zip curl \
 && rm -rf /var/lib/apt/lists/*

# PHP extensions needed by Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install without running artisan (avoids DB/env errors during build)
ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install --ignore-platform-reqs --no-interaction --no-scripts --optimize-autoloader

# Expose (Railway injects $PORT)
EXPOSE 8000

# Entrypoint (runs artisan after env vars exist)
CMD ["sh", "./entrypoint.sh"]
