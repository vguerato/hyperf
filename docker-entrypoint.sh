#!/bin/sh
set -e

echo "Installing dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "Running migrations..."
php bin/hyperf.php migrate --force

echo "Starting Hyperf server..."
exec php bin/hyperf.php start
