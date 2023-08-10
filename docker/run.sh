#!/usr/bin/env bash

echo "running run.sh"
composer install --ignore-platform-reqs
php /var/www/html/docker/ptrace/src/p-trace.php &