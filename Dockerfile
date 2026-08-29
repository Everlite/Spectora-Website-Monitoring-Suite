FROM php:8.4-apache

# Enable Apache mod_rewrite for Laravel
RUN a2enmod rewrite

# Update Apache DocumentRoot to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# System dependencies
RUN apt-get update && apt-get install -y \
    supervisor \
    zip \
    unzip \
    git \
    curl \
    gnupg \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js for Vite build
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# PHP Extensions
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip intl

# Apache listens on 8080 (non-privileged port; workers run as www-data)
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:8080>/' /etc/apache2/sites-available/000-default.conf

# Copy Supervisor configuration (schedule:work + queue — no system cron daemon)
COPY ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Pre-create directories for volumes and set permissions
RUN mkdir -p /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/database /var/www/html/storage /var/www/html/bootstrap/cache

# Copy application files
COPY . .

# Install PHP dependencies (auto-sync lockfile if updated)
RUN composer install --no-dev --optimize-autoloader || composer update --no-dev --optimize-autoloader

# Build frontend assets (Vite + Tailwind + Alpine.js)
RUN npm install && npm run build && rm -rf node_modules

# Copy startup script
COPY ./docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Final permission check
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/www/html/public/build \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Expose port 8080 (map to host 8000 in compose)
EXPOSE 8080

# Use custom entrypoint for automation (keys, migrations, supervisor)
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
