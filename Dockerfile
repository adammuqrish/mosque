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

# Install Node.js & npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && apt-get install -y nodejs

COPY package.json package-lock.json ./

# Install npm dependencies
RUN npm ci --no-audit --no-fund

COPY composer.json composer.lock ./

# Create .env from example
COPY .env.example .env

# Install dependencies without scripts (avoid key:generate during build)
RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Copy full project (includes artisan file)
COPY . .

# Compile frontend assets
RUN npm run production --no-interaction 2>/dev/null || echo "Asset compilation skipped"

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
COPY start.sh /var/www/html/start.sh

RUN chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod +x /var/www/html/start.sh

EXPOSE 8080

CMD ["bash", "/var/www/html/start.sh"]