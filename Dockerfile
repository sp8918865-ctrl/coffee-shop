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

# Install system dependencies, unzip, git, & zip extension
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

# Atasi konflik MPM Apache & ubah port default ke 8080 untuk Railway
RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork \
    && sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

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

# Jalankan composer install
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader --no-scripts \
    && composer dump-autoload --no-dev --optimize --no-scripts \
    && php artisan package:discover --ansi \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Salin hasil build frontend dari stage 1
COPY --from=frontend /app/public/build ./public/build

EXPOSE 8080