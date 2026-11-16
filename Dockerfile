# 1. Frontend Build Stage
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

# 2. PHP & Apache Production Stage
FROM php:8.3-apache

# Set environment variable composer superuser di dalam stage PHP
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies, unzip, git, & zip extension (HARUS SEBELUM composer install)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libonig-dev \
        libpq-dev \
        libxml2-dev \
        unzip \
        git \
        libzip-dev \
    && docker-php-ext-install mbstring opcache pdo_pgsql xml zip \
    && rm -rf /var/lib/apt/lists/*

# Konfigurasi Apache untuk Laravel
RUN a2enmod rewrite headers \
    && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && printf '%s\n' \
        '<Directory /var/www/html/public>' \
        '    AllowOverride All' \
        '    Require all granted' \
        '</Directory>' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Salin Composer binary resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin seluruh file proyek
COPY . .

# Jalankan composer install (sekarang ekstensi zip dan unzip sudah tersedia)
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader --no-scripts \
    && composer dump-autoload --no-dev --optimize --no-scripts \
    && php artisan package:discover --ansi \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Salin hasil build frontend dari stage 1
COPY --from=frontend /app/public/build ./public/build

# Konfigurasi Entrypoint (Opsional, pastikan file filenya ada jika ingin dipakai)
# COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
# RUN chmod +x /usr/local/bin/docker-entrypoint

EXPOSE 8080

# CMD ["docker-entrypoint"]