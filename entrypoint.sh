#!/usr/bin/env sh
set -e
# cd /var/www

[ -f .env ] || { echo "[entrypoint] Creating .env"; cp .env.example .env; }

if [ ! -d vendor ]; then
  echo "[entrypoint] composer install..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if ! grep -q '^APP_KEY=' .env || [ -z "$(grep '^APP_KEY=' .env | cut -d= -f2)" ]; then
  php artisan key:generate --force
fi

exec php-fpm
