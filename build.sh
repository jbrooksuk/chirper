#!/usr/bin/env bash

rm -rf database/database.sqlite
touch database/database.sqlite

php artisan migrate --env=docs
php artisan scribe:generate --env=docs
