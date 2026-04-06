#!/bin/bash
set -e

echo "=== Starting Laravel application ==="
echo "PORT is: ${PORT:-8080}"

# Clear any cached config
php artisan config:clear || true
php artisan route:clear || true  
php artisan view:clear || true

# Create storage link for public files
php artisan storage:link --force || true

# Run migrations
php artisan migrate --force || echo "Migrations skipped"

echo "=== Starting server on port ${PORT:-8080} ==="

# Start the server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}