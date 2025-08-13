FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev zip curl \
 && rm -rf /var/lib/apt/lists/*

# Install PHP extensions needed by Laravel
RUN docker-php-ext-install pdo_mysql mbstring bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Make entrypoint executable
RUN chmod +x /app/entrypoint.sh

# Install PHP dependencies without running artisan
ENV COMPOSER_MEMORY_LIMIT=-1
RUN composer install --ignore-platform-reqs --no-interaction --no-scripts --optimize-autoloader

# Expose port (Railway uses $PORT)
EXPOSE 8000

# Start app
CMD ["sh", "./entrypoint.sh"]
