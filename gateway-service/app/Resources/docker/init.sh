#!/bin/bash

cd /var/www

echo "[info] copy default parameters.yml"
cp -n ./app/config/parameters.yml.dist ./app/config/parameters.yml
chown www:www ./app/config/parameters.yml

echo "[info] Running composer"
composer install --optimize-autoloader --working-dir=/var/www

echo "[info] Changing permissions for storage/"
chmod -R 777 ./var ./web

php ./bin/console cache:clear --no-warmup

chown -R www:www ./var ./vendor ./web
