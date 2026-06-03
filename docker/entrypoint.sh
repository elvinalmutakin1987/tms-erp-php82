#!/bin/sh

set -e

cd /var/www/html

mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Waiting for database..."

    for i in $(seq 1 30); do
        php -r '
        try {
            new PDO(
                "mysql:host=" . getenv("DB_HOST") . ";port=" . getenv("DB_PORT") . ";dbname=" . getenv("DB_DATABASE"),
                getenv("DB_USERNAME"),
                getenv("DB_PASSWORD")
            );
            exit(0);
        } catch (Throwable $e) {
            exit(1);
        }
        ' && break

        echo "Database not ready yet..."
        sleep 2

        if [ "$i" = "30" ]; then
            echo "Database connection failed."
            exit 1
        fi
    done

    php artisan migrate --force
fi

php artisan storage:link || true
php artisan optimize:clear
php artisan optimize
php artisan migrate --seed --force

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf