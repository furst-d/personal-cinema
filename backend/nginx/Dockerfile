# Use the official Nginx image as a parent image
FROM nginx:alpine

## Copy nginx configuration files
COPY nginx/default.conf /etc/nginx/conf.d/default.conf
COPY nginx/fastcgi-php.conf /etc/nginx/fastcgi-php.conf

# Copy application source code
COPY symfony/public /var/www/public

# Expose port
EXPOSE 8080
