# Use the official PHP-Apache image
FROM php:8.2-apache

# Enable Apache mod_rewrite (optional, for clean URLs)
RUN a2enmod rewrite

# Set working directory inside the container
WORKDIR /var/www/html

# Copy project files to the container
COPY . /var/www/html/

# Set correct permissions (optional)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80 (default for Apache)
EXPOSE 80
