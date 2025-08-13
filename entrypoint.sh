#!/bin/sh
set -e

# Ensure Laravel can read Railway's env vars each deploy
php artisan config:clear || true
php artisan config:cache || true

# Start Laravel using PHP's built-in server
php -S 0.0.0.0:${PORT:-8000} -t public
