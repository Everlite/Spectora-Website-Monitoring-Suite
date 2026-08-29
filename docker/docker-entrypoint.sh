#!/bin/sh

# ---------------------------------------------------------------------
# Spectora Agency Edition - Docker Entrypoint Script
# ---------------------------------------------------------------------

set -e

# 1. Fix permissions on mounted volumes & Apache directories
echo "Setting permissions on storage, database and apache logs..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

mkdir -p /var/log/apache2 /var/run/apache2 /var/lock/apache2
chown -R www-data:www-data /var/log/apache2 /var/run/apache2 /var/lock/apache2
chmod -R 775 /var/log/apache2 /var/run/apache2 /var/lock/apache2

# Ensure required storage subdirectories exist
mkdir -p /var/www/html/storage/logs \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/app/public/logos
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

# 2. Ensure .env exists
if [ ! -f .env ]; then
    if [ -w . ]; then
        echo "Creating .env from .env.example..."
        cp .env.example .env
    else
        echo "WARNING: .env does not exist and directory is not writable. Cannot copy .env.example."
    fi
fi

# 3. Check for APP_KEY (safely handling missing keys under set -e)
CURRENT_KEY=""
if [ -f .env ] && [ -r .env ]; then
    CURRENT_KEY=$(grep "^APP_KEY=" .env 2>/dev/null | cut -d'=' -f2 | tr -d ' ' || true)
fi

if [ -z "$CURRENT_KEY" ] || [ "$CURRENT_KEY" = "base64:PLACEHOLDER" ]; then
    if [ -f .env ] && [ -w .env ]; then
        echo "APP_KEY is missing/placeholder. Generating new key..."
        php artisan key:generate --force
    else
        echo "WARNING: APP_KEY is empty/placeholder and .env is not writable. Skipping key:generate."
    fi
else
    echo "APP_KEY detected. Skipping generation."
fi

# 4. Handle SQLite database (Move to storage to avoid volume-masking migrations)
OLD_DB="/var/www/html/database/database.sqlite"
NEW_DB="/var/www/html/storage/database.sqlite"

if [ -f "$OLD_DB" ] && [ ! -f "$NEW_DB" ]; then
    echo "Found database in old location. Moving to storage for better persistence..."
    mv "$OLD_DB" "$NEW_DB"
fi

if [ ! -f "$NEW_DB" ]; then
    echo "Creating new database in storage..."
    touch "$NEW_DB"
fi

chown www-data:www-data "$NEW_DB"
chmod 775 "$NEW_DB"

# 5. Clear all caches (config, route, view, OPcache) to ensure fresh state
echo "Clearing application caches..."
php artisan optimize:clear

# 6. Run migrations
echo "Running database migrations..."
# Point to the database in storage for the migration command
DB_DATABASE="$NEW_DB" php artisan migrate --force

# 7. Create storage link (forces overwriting existing links/files if present)
echo "Ensuring storage link..."
php artisan storage:link --force

# Clean up any stale PID files from unclean container shutdowns
rm -f /var/run/apache2/apache2.pid /var/run/apache2.pid /var/run/supervisord.pid /run/apache2/apache2.pid /run/httpd.pid 2>/dev/null || true

# 8. Start Supervisor (Apache + Cron + Queue Worker)
echo "Starting services via Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
