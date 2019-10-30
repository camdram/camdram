#!/bin/sh
set -e

# Sleep while we wait for MariaDB to startup
sleep 7

if [ ! -d /usr/src/camdram/vendor ]; then
  sleep 10
  /usr/local/bin/composer install
  php app/console doctrine:database:create
  php app/console doctrine:migrations:migrate --no-interaction
  php app/console doctrine:fixtures:load --no-interaction
  php app/console camdram:assets:download
  python3 ./gen-csp-hashes.py
fi

php app/console server:run 0.0.0.0:8000
