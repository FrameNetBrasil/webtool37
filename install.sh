#!/usr/bin/env bash

echo "running install.sh"
cd /var/www/html || exit
composer install --ignore-platform-reqs
cd /var/www/html/apps/webtool || exit
composer install --ignore-platform-reqs
cd /var/www/html/core || exit
chmod -R 777 var
[ ! -f /var/www/html/apps/webtool/conf/conf.php ] && cp /var/www/html/apps/webtool/conf/conf.dist.php /var/www/html/apps/webtool/conf/conf.php
[ ! -f /var/www/html/core/conf/conf.php ] && cp /var/www/html/core/conf/conf.dist.php /var/www/html/core/conf/conf.php
[ ! -f /var/www/html/.env ] && cp /var/www/html/.env.dist /var/www/html/.env
apache2-foreground