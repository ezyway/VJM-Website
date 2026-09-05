FROM php:8.2-apache

# Enable Apache mod_rewrite, mod_headers, and mod_expires
RUN a2enmod rewrite headers expires

# Set working directory
WORKDIR /var/www/html

# Copy application source code to the container web root
COPY website/ /var/www/html/

# Adjust file permissions for Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
