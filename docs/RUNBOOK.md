# Spectora Operations Runbook

## Deploy / Update

```bash
git pull
docker compose -f docker-compose.prod.yml up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan config:cache
```

For local development with live code mounts, use `docker-compose.yml` instead.

## Backups (SQLite)

```bash
# From project root (adjust DB path if needed)
DB_DATABASE=./storage/database.sqlite ./scripts/backup-sqlite.sh
```

Copy the `backups/` folder off the server regularly. With WAL mode, include `-wal` and `-shm` sidecar files when present.

## Queue Worker

Scheduled checks dispatch `CheckUrlJob` to the database queue. Ensure Supervisor (Docker image) or `php artisan queue:work` is running.

## Health

- HTTP: `GET /up` (Laravel health route)
- Login page: Docker healthcheck uses `/login`

## Production Checklist

- `APP_DEBUG=false`
- `SESSION_ENCRYPT=true`
- `APP_URL=https://…`
- `TRUSTED_PROXIES` when behind a reverse proxy
- Working `MAIL_*` for alerts and monthly digests
- Optional VAPID keys for Web Push
