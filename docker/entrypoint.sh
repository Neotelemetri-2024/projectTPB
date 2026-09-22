#!/bin/sh
set -e

mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache /run/php
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache

if [ ! -L /var/www/public/storage ]; then
    su -s /bin/sh www-data -c 'php /var/www/html/artisan storage:link' || true
fi

if [ -n "${APP_KEY:-}" ] || grep -q '^APP_KEY=.' /var/www/.env 2>/dev/null; then
    su -s /bin/sh www-data -c 'php /var/www/html/artisan optimize' || true
fi

nginx -g 'daemon off;' &
exec php-fpm -F
