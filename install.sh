#!/usr/bin/env bash

echo "running install.sh"
cd /var/www/html || exit
composer install --ignore-platform-reqs
cd /var/www/html/core || exit
chmod -R 777 var
[ ! -f /var/www/html/.env ] && cp /var/www/html/.env.dist /var/www/html/.env
apache2-foreground