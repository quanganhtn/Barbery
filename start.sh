#!/bin/sh

python3 -m uvicorn barbery_ai.main:app --host 127.0.0.1 --port 8001 &

php artisan key:generate --force || true
php artisan config:clear || true
php artisan cache:clear || true
php artisan migrate --force || true

php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
