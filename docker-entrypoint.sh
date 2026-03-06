#!/bin/bash
set -e

echo "Starting Laravel application..."

# Run migrations (allow failure for first deploy)
php artisan migrate --force || echo "Migrations skipped or failed"

# Clear any cached config (important for Railway)
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "Starting server on port ${PORT:-8080}..."

# Start the server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
