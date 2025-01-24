#!/bin/bash

sudo apache2ctl -D FOREGROUND

mkdir -p /etc/coredns
# a2enmod rewrite
# service apache2 start
# service apache2 status
echo "Clearing Laravel caches and starting fresh..."
php artisan config:clear
php artisan cache:table
php artisan cache:clear

php artisan route:clear
php artisan view:clear
php artisan event:clear
php artisan clear-compiled
php artisan optimize:clear

echo "Refreshing database..."
php artisan migrate:refresh --seed

#npm install && npm run dev

echo "All done. Laravel is reset!"

