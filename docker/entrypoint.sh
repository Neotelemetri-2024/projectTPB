#!/bin/sh
set -e

mkdir -p /var/www/storage /var/www/bootstrap/cache /run/php
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R ug+rwX /var/www/storage /var/www/bootstrap/cache

if [ ! -L /var/www/public/storage ]; then
    su -s /bin/sh www-data -c 'php artisan storage:link' || true
fi

if [ -n "${APP_KEY:-}" ] || grep -q '^APP_KEY=.' /var/www/.env 2>/dev/null; then
    su -s /bin/sh www-data -c 'php artisan optimize' || true
fi

nginx -g 'daemon off;' &
exec php-fpm -F
