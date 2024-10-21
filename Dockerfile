# Dockerfile

# PHP image 
FROM php:8.2-apache

# Installing necessary PHP extensions and other dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    git \
    unzip \
    && docker-php-ext-install \
    intl \
    pdo_mysql \
    zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

# Step 3: Enable Apache Rewrite module
RUN a2enmod rewrite

# Step 4: Copy Symfony project files to the working directory
WORKDIR /var/www/html
COPY . .

# Step 5: Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Step 6: Run Composer install to install dependencies
RUN composer install

# Step 7: Set permissions for Symfony folder
RUN chown -R www-data:www-data /var/www/html/var
RUN chmod -R 775 /var/www/html/var

# Step 8: Expose port 80
EXPOSE 80

# Step 9: Start Apache
CMD ["apache2-foreground"]
