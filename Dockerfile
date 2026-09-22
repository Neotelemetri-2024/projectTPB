FROM node:20-alpine AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js tailwind.config.js ./
RUN npm run build

FROM php:8.3-fpm

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    cron \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader
COPY --from=frontend /app/public/build /var/www/public/build

RUN rm -f /etc/nginx/sites-enabled/default \
    && mkdir -p /run/php /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/nginx/conf.d/app.conf /etc/nginx/sites-available/default
RUN rm -f /etc/nginx/sites-enabled/default \
    && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
