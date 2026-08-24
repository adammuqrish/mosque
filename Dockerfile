# Build: v3 - PHP 8.3 for Laravel 12
FROM php:8.3-cli AS builder

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

# Install Node.js & npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && apt-get install -y nodejs

COPY package.json package-lock.json ./

# Install npm dependencies
RUN npm ci --no-audit --no-fund

COPY composer.json composer.lock ./

# Install dependencies without scripts (avoid key:generate during build)
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Copy full project (includes artisan file)
COPY . .

# Remove local cache and .env that may have been copied
RUN rm -f bootstrap/cache/config.php .env

# Compile frontend assets
RUN npm run production --no-interaction 2>/dev/null || echo "Asset compilation skipped"

# Run artisan commands after vendor exists
RUN php artisan package:discover --ansi

FROM php:8.3-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    default-mysql-client \
    nginx

RUN docker-php-ext-install pdo_mysql zip gd

COPY --from=builder /var/www/html /var/www/html
COPY nginx.conf /etc/nginx/nginx.conf
COPY start.sh /var/www/html/start.sh

RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod +x /var/www/html/start.sh

EXPOSE 8080

CMD ["bash", "/var/www/html/start.sh"]
