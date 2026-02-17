#!/bin/sh
set -e

# Default PORT to 80 if not set (Railway provides PORT env var)
export PORT=${PORT:-80}

# Substitute PORT in nginx config template
# Substitute PORT in nginx config template
envsubst '${PORT}' < /etc/nginx/sites-available/default.template > /etc/nginx/sites-enabled/default

# Verify Nginx config
nginx -t

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Cache config and views (skip route:cache to avoid closure issues)
echo "Caching configuration..."
php artisan config:cache
php artisan view:cache

# Start Supervisord
echo "Starting Supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
