#!/bin/sh

# ---------------------------------------------------------------------
# Spectora Agency Edition - Docker Entrypoint Script
# ---------------------------------------------------------------------

set -e

# 1. Ensure .env exists
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

# 2. Check for APP_KEY
if [ -z "$(grep -E "^APP_KEY=" .env | cut -d'=' -f2)" ]; then
    echo "APP_KEY missing. Generating new key..."
    php artisan key:generate --force
fi

# 3. Ensure SQLite database exists and has correct permissions
touch /var/www/html/database/database.sqlite
chown www-data:www-data /var/www/html/database/database.sqlite
chmod 775 /var/www/html/database/database.sqlite

# 4. Automate Migrations (Agency Edition is always ahead)
echo "Running database migrations..."
php artisan migrate --force

# 5. Create storage link if missing
if [ ! -d public/storage ]; then
    echo "Creating storage link..."
    php artisan storage:link
fi

# 6. Optional: Optimize (Only for production feel)
# php artisan config:cache
# php artisan route:cache

# 7. Start Supervisor (which manages Apache and Cron)
echo "Starting services via Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
