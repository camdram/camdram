#!/bin/sh
set -e

if [ ! -d /usr/src/camdram/vendor ]; then
  /usr/local/bin/composer install
  app/console camdram:database:refresh
  app/console camdram:assets:download
  php app/console camdram:assets:download
  python3 ./gen-csp-hashes.py
fi

php app/console server:run 0.0.0.0:8000
