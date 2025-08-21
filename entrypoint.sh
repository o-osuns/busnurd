#!/bin/bash

# Only create the .env if it doesn't exist
if [ ! -f .env ]; then
  echo "[entrypoint] Creating .env from .env.example..."
  cp .env.example .env
  php artisan key:generate
else
  echo "[entrypoint] .env already exists. Skipping setup."
fi

# Continue with the main container process
exec php-fpm
