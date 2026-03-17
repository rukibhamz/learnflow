#!/usr/bin/env bash
set -euo pipefail

git pull

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

if [ -f package-lock.json ]; then
  npm ci
fi

npm run build

php artisan migrate --force

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

php artisan horizon:terminate
php artisan queue:restart

