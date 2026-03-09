#!/bin/bash

set -e

echo "🚀 Starting Reolink Streamer setup..."

# Set proper permissions
echo "📁 Setting file permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --optimize-autoloader

# Install NPM dependencies
echo "📦 Installing NPM dependencies..."
npm install

# Generate Laravel key if not exists
echo "🔑 Setting up Laravel..."
if ! grep -q "APP_KEY=base64:" /var/www/html/.env 2>/dev/null; then
    php artisan key:generate --force
fi

# Create database if it doesn't exist
echo "🗄️ Setting up database..."
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi
chown www-data:www-data /var/www/html/database/database.sqlite
chmod 664 /var/www/html/database/database.sqlite

# Run migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# Build assets (now that vendor directory exists)
echo "🎨 Building assets..."
npm run build

echo "✅ Setup complete! Starting services..."

# Start supervisor to manage all processes
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf