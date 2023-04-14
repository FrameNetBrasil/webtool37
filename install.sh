#!/usr/bin/env bash

echo "running install.sh"
cd /var/www/html || exit
composer install --ignore-platform-reqs
cd /var/www/html/apps/webtool || exit
composer install --ignore-platform-reqs
cd /var/www/html/core || exit
chmod -R 777 var
apache2-foreground