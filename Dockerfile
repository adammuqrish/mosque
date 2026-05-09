FROM php:8.1-cli AS builder

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl

RUN docker-php-ext-install pdo_mysql zip gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

# Create .env from example
COPY .env.example .env

# Install dependencies without scripts (avoid key:generate during build)
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Run artisan commands after vendor exists
RUN php artisan package:discover --ansi

FROM php:8.1-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl

RUN docker-php-ext-install pdo_mysql zip gd

COPY --from=builder /var/www/html /var/www/html

RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE $PORT

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=$PORT"]