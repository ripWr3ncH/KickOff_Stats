#!/bin/bash
set -e

echo "==> Setting up SQLite database..."
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite
chown www-data:www-data /var/www/html/database/database.sqlite
chmod 664 /var/www/html/database/database.sqlite

echo "==> Running migrations and seeding..."
php /var/www/html/artisan migrate:fresh --seed --force

echo "==> Caching config and routes..."
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
chown -R www-data:www-data /var/www/html/storage
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache
php /var/www/html/artisan view:cache

echo "==> Starting Apache..."
exec apache2-foreground
