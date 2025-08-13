#!/bin/sh
set -e

# Ensure Laravel can read Railway's env vars each deploy
php artisan config:clear || true
php artisan config:cache || true

# Convert Railway's $PORT to integer (default 8000 if unset)
PORT_INT=${PORT:-8000}

# Start Laravel on Railway's assigned port
php artisan serve --host=0.0.0.0 --port=$PORT_INT
