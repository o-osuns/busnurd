FROM php:8.3-fpm

LABEL maintainer="Olaleye Osunsanya"
LABEL description="Busnurd Application"
LABEL email="olaleye.osunsanya@gmail.com"

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd xml

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --optimize-autoloader

RUN chmod +x entrypoint.sh

EXPOSE 9000
CMD ["php-fpm"]
