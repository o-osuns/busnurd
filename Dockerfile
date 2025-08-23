FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip zip curl libpng-dev libonig-dev libxml2-dev libpq-dev \
    libjpeg62-turbo-dev libfreetype6-dev \
 && ln -s /usr/lib/x86_64-linux-gnu/libjpeg.so /usr/lib/libjpeg.so \
 && ln -s /usr/lib/x86_64-linux-gnu/libjpeg.a /usr/lib/libjpeg.a \
 && docker-php-ext-configure gd --with-freetype --with-jpeg=/usr \
 && docker-php-ext-install pdo pdo_pgsql mbstring gd bcmath

# Composer CLI
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install composer dependencies as root
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-autoloader

# Copy application code
COPY . .

# Complete the autoloader generation and run scripts
RUN composer dump-autoload --optimize

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Install Node dependencies
RUN npm install --production

# Set proper permissions for Laravel directories
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Match container user to your Ubuntu user (prevents root-owned files)
ARG UID=1000
ARG GID=1000
RUN groupmod -o -g ${GID} www-data \
 && usermod  -o -u ${UID} -g ${GID} www-data

USER www-data
EXPOSE 9000
CMD ["php-fpm"]
