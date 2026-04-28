#!/bin/bash
set -e

echo "Deploing..."
git pull origin main
php artisan down
php composer.phar install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
php artisan up

echo "Done!"