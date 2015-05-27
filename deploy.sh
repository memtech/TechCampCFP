#!/bin/sh
cp config/production.dist.yml config/production.yml
composer install
vendor/bin/phinx migrate --environment=production