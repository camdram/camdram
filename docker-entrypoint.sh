#!/bin/sh
set -e

if [ ! -d /usr/src/camdram/vendor ]; then
  /usr/local/bin/composer install
  app/console camdram:database:refresh
  app/console camdram:assets:download
fi
