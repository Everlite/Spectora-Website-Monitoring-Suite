## v0.2.0

Second stable release. Builds on **v0.1.0** with a major stability/security pass, privacy-friendly analytics geo, and ops improvements.

### Highlights

- **Analytics geo:** country, region, city (Cloudflare headers or GeoLite2-City); Top Countries / Top Cities UI
- **Privacy:** [`docs/PRIVACY.md`](docs/PRIVACY.md), truncated IP before visitor hash, no raw IP storage
- **Ops:** scheduler heartbeat, `GET /health/ops` for Docker healthchecks, queue worker documented as required
- **Fixes:** Analyze button, Edge/Opera browser stats, admin setup `is_admin`, CI/monitoring URL tests
- **Security:** SSRF pinning, alert routing to owners + admins, monitored URL validation, expanded tests

### Upgrade from v0.1.0

```bash
git pull
docker compose -f docker-compose.prod.yml up -d --build   # or your deploy path
docker compose exec app php artisan migrate --force
```

Set `TRUSTED_PROXIES` (Cloudflare) or add `GeoLite2-City.mmdb` under `storage/app/geoip/` for city analytics.

Full details: [CHANGELOG.md](https://github.com/Everlite/Spectora-Website-Monitoring-Suite/blob/main/CHANGELOG.md#020---2026-06-04).
