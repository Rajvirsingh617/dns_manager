# Stage 1: Laravel and Apache setup
FROM php:8.2-apache AS laravel-apache

# Set the ServerName directive
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Install required dependencies for Laravel
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    apache2-utils \
    ca-certificates \
    coreutils \
    sudo \
    vim \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer



# Set up Laravel application
WORKDIR /var/www/html
COPY ./laravel /var/www/html
RUN composer install --no-dev --optimize-autoloader

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy Apache configuration for Laravel
COPY ./laravel/laravel.conf /etc/apache2/sites-available/000-default.conf

# Copy entrypoint script
COPY ./entrypoint.sh /entrypoint.sh

# Make sure the entrypoint script is executable
RUN chmod +x /entrypoint.sh

# Expose ports
EXPOSE 80

# Set the entrypoint to run the script
ENTRYPOINT ["/entrypoint.sh"]
