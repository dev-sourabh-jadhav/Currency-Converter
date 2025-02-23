FROM php:8.1-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Set the working directory
WORKDIR /var/www/html

# Copy Laravel files
COPY . .

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]
