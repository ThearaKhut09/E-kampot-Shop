#!/usr/bin/env bash

set -e

echo "Starting Laravel deployment on Render..."

# Create required directories
mkdir -p storage/logs
mkdir -p storage/framework/{cache,sessions,views}
mkdir -p bootstrap/cache

# Install Composer dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets
echo "Installing Node.js dependencies..."
npm ci

echo "Building frontend assets..."
npm run build

# Set proper permissions
echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache

# Create SQLite database file if it doesn't exist
echo "Setting up SQLite database..."
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi
chmod 664 database/database.sqlite

# Copy production environment file
if [ ! -f .env ]; then
    cp .env.production .env
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear and cache configuration
echo "Optimizing Laravel..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Seed the database (optional - uncomment if you want to run seeders)
# php artisan db:seed --force

# Create storage link
echo "Creating storage link..."
php artisan storage:link

echo "Laravel deployment completed successfully!"
