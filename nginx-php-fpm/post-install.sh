#!/usr/bin/env sh

echo "Docker post-install"

php /var/www/html/artisan telescope:install
php /var/www/html/artisan migrate
php /var/www/html/artisan telescope:publish

chown 1000:1000 /var/www/html -R 
