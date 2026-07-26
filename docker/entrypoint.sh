#!/bin/sh
set -eu

cd /var/www/html

mkdir -p \
    bootstrap/cache \
    public/avatars \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

chown -R www-data:www-data bootstrap/cache public/avatars storage

php artisan package:discover

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Aguardando o banco de dados..."

    attempt=1
    until php -r '
        new PDO(
            "mysql:host=".getenv("DB_HOST").";port=".getenv("DB_PORT"),
            getenv("DB_USERNAME"),
            getenv("DB_PASSWORD")
        );
    ' >/dev/null 2>&1; do
        if [ "$attempt" -ge 30 ]; then
            echo "Não foi possível conectar ao banco de dados."
            exit 1
        fi

        attempt=$((attempt + 1))
        sleep 2
    done

    php artisan migrate --force
fi

if [ "${APP_ENV:-production}" = "production" ]; then
    php artisan config:cache
    php artisan view:cache
else
    php artisan config:clear
    php artisan view:clear
fi

if [ -n "${VITE_DEV_SERVER_URL:-}" ]; then
    printf '%s' "$VITE_DEV_SERVER_URL" > public/hot
else
    rm -f public/hot
fi

touch /tmp/tarefy-ready

exec "$@"
