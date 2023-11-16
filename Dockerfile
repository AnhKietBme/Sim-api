FROM php:8.2-cli

# Install system dependencies
RUN apt-get update -y && apt-get upgrade -y && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev

# Install PHP extensions
RUN docker-php-ext-install sockets zip
RUN docker-php-ext-enable sockets

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

EXPOSE 8080