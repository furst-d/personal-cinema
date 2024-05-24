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
    && docker-php-ext-install pdo mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create a non-root user
RUN groupadd -g 1000 appuser && useradd -r -u 1000 -g appuser appuser

# Copy existing application directory contents
COPY symfony /var/www

# Set ownership to the non-root user
RUN chown -R appuser:appuser /var/www

# Switch to the non-root user
USER appuser

# Set environment variable to allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install Symfony dependencies
RUN composer install --no-scripts

# Switch back to root to copy nginx configuration file
USER root

# Copy nginx configuration files
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./nginx/fastcgi-php.conf /etc/nginx/fastcgi-php.conf

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]
