#!/bin/sh
set -e

# Ensure Laravel can read Railway's env vars each deploy
php artisan config:clear || true
php artisan config:cache || true

# Start Laravel on Railway's assigned port
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
