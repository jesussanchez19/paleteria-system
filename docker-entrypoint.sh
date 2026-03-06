#!/bin/bash
set -e

echo "Starting Laravel application..."

# Run migrations (allow failure for first deploy)
php artisan migrate --force || echo "Migrations skipped or failed"

# Cache configuration
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Starting server on port ${PORT:-8080}..."

# Start the server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
