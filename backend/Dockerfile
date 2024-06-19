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
    nginx \
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
RUN composer install --no-scripts --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Copy application source code
COPY symfony /var/www

# Copy nginx configuration files
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./nginx/fastcgi-php.conf /etc/nginx/fastcgi-php.conf

# Set ownership to the non-root user
RUN chown -R appuser:appuser /var/www

# Ensure logs directory exists and has correct permissions
RUN mkdir -p /var/www/var/log && chown -R appuser:appuser /var/www/var/log && chmod -R 775 /var/www/var/log

# Expose ports
EXPOSE 80
EXPOSE 443
EXPOSE 9000

# Start nginx and php-fpm
CMD ["sh", "-c", "nginx && php-fpm"]
