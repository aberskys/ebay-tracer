#!/bin/bash

cd /var/www

echo "[info] copy default parameters.yml"
cp -n ./app/config/parameters.yml.dist ./app/config/parameters.yml

sed -i "s/\(^\s\+\)database_user:.*$/\1database_user: $MYSQL_USER/gm" ./app/config/parameters.yml

if [ ! -z "$MYSQL_PORT" ] ; then
    sed -i "s/\(^\s\+\)database_port:.*$/\1database_port: $MYSQL_PORT/gm" ./app/config/parameters.yml
else
    sed -i "s/\(^\s\+\)1database_port:.*$/\1database_port: 3306/gm" ./app/config/parameters.yml
fi

sed -i "s/\(^\s\+\)database_name:.*$/\1database_name: $MYSQL_DATABASE/gm" ./app/config/parameters.yml
sed -i "s/\(^\s\+\)database_password:.*$/\1database_password: $MYSQL_PASSWORD/gm" ./app/config/parameters.yml

echo "[info] Running composer"
composer install --optimize-autoloader --working-dir=/var/www

echo "[info] Changing permissions for storage/"
chmod -R 777 ./var ./web

echo "[info] Waiting for mysql"
sleep 10

echo "[info] Migrating database"
php ./bin/console cache:clear --no-warmup
php ./bin/console doctrine:migrations:migrate --no-interaction
php ./bin/console app:fixtures --no-interaction

chown -R www:www ./var ./vendor ./web
