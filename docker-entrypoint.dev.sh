#!/bin/sh
set -e

mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache /run/php
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache

if [ ! -f /var/www/html/vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ ! -L /var/www/html/public/storage ]; then
    su -s /bin/sh www-data -c 'php /var/www/html/artisan storage:link' || true
fi

su -s /bin/sh www-data -c 'php /var/www/html/artisan optimize:clear' || true

nginx -g 'daemon off;' &
exec php-fpm -F
