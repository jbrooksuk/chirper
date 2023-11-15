#!/usr/bin/env bash

php artisan \
    migrate:fresh --env=docs
php artisan \
    scribe:generate --env=docs
