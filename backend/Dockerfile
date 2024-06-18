# Use the official PHP image as a parent image
FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-install pdo mbstring exif pcntl bcmath gd pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a non-root user
RUN groupadd -g 1000 appuser && useradd -r -u 1000 -g appuser appuser

# Set working directory
WORKDIR /var/www

# Copy Composer configuration and install dependencies
COPY symfony/composer.json symfony/composer.lock ./

# Set environment variable to allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-scripts --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    && composer show symfony/runtime

# Copy application source code
COPY symfony /var/www

# Copy environment file
COPY .env /var/www/.env

# Set ownership to the non-root user
RUN chown -R appuser:appuser /var/www

# Ensure logs directory exists and has correct permissions
RUN mkdir -p /var/www/var/log && chown -R appuser:appuser /var/www/var/log && chmod -R 775 /var/www/var/log

# Switch to the non-root user
USER appuser

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
