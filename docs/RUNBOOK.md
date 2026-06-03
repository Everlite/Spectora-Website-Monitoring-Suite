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

## Queue Worker (required)

Scheduled checks dispatch `CheckUrlJob` to the database queue. **Without a running worker, domains are not checked.**

- Docker: Supervisor program `queue-worker` (user `www-data`)
- Manual: `php artisan queue:work database --sleep=3 --tries=3`

Verify: `docker compose exec app supervisorctl status` or watch `jobs` / `failed_jobs` tables.

## Scheduler (required)

Docker runs `php artisan schedule:work` as **www-data** (Supervisor program `scheduler`). Do not rely on system cron inside the container.

Bare-metal: use cron `* * * * * cd /path && php artisan schedule:run` or a systemd unit running `schedule:work`.

## Health

- `GET /up` — Laravel default health (app boot)
- `GET /health/ops` — **loopback only** (`127.0.0.1` / `::1`); returns 503 if scheduler heartbeat is older than 20 minutes
- Docker `healthcheck` uses `http://127.0.0.1:8080/health/ops` (Apache listens on **8080**, workers as `www-data`)

After deploy, confirm heartbeat: `docker compose exec app php artisan schedule:run` then `curl -f http://127.0.0.1:8080/health/ops` inside the container.

## Pre-release gate

See [`STAGING_CHECKLIST.md`](STAGING_CHECKLIST.md) before tagging a release.

## Production Checklist

- `APP_DEBUG=false`
- `SESSION_ENCRYPT=true`
- `APP_URL=https://…`
- `TRUSTED_PROXIES` when behind a reverse proxy (also enables Cloudflare geo headers for analytics)
- Optional `ANALYTICS_GEOLITE2_PATH` or `storage/app/geoip/GeoLite2-City.mmdb` for city/country without Cloudflare
- Working `MAIL_*` for alerts and monthly digests
- Optional VAPID keys for Web Push
