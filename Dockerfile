FROM php:8.3.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    && docker-php-ext-install zip

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html
