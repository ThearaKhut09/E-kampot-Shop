#!/bin/bash

# Laravel deployment script for Docker
set -e

echo "Starting Laravel deployment..."

# Copy .env.production if .env doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env from .env.production..."
    cp .env.production .env
fi

# Generate APP_KEY if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Clear all caches
echo "Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimize for production
echo "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

# Set proper permissions
echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache
chmod 664 database/database.sqlite

echo "Laravel deployment completed!"
