FROM php:8.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    nginx \
    supervisor \
    sqlite3 \
    libsqlite3-dev \
    gettext-base

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip intl

# Install Node.js for Vite asset build
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install PHP dependencies (production only)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Build frontend assets
RUN npm ci && npm run build

# Copy nginx config as template (PORT will be substituted at runtime)
COPY docker/nginx/app.conf /etc/nginx/sites-available/default.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod +x docker/entrypoint.sh

# Expose port (Railway overrides this with PORT env)
EXPOSE 80

# Start supervisor
ENTRYPOINT ["docker/entrypoint.sh"]
