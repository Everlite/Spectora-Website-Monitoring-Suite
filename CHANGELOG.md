# Changelog

All notable changes to [Spectora](https://github.com/Everlite/Spectora-Website-Monitoring-Suite) are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project uses [Semantic Versioning](https://semver.org/) for release tags (`MAJOR.MINOR.PATCH`).

**How we maintain it**

- Every user-facing change on `main` gets an entry under **`[Unreleased]`** while it is being developed.
- When you cut a release (or ship a milestone to production), move those bullets into a new **`[x.y.z] - YYYY-MM-DD`** section and clear `[Unreleased]`.
- Group entries under: `Added`, `Changed`, `Fixed`, `Security`, `Deprecated`, `Removed`.
- Link to this file from [README.md](README.md) when upgrading between versions.

---

## [Unreleased]

### Planned

- Sub-URL downtime alerts (not only the main domain URL)
- Blade component split for `dashboard.blade.php`
- Optional Docker hardening (Apache non-root)

---

## [1.1.0] - 2026-06-04

### Added

- Privacy-friendly analytics geo: country, region, and city (per-domain precision: `off` / `country` / `city`)
- Top Countries / Top Cities in the analytics UI
- Geo resolution via Cloudflare headers (`TRUSTED_PROXIES`) or local GeoLite2-City (`geoip2/geoip2`, `ANALYTICS_GEOLITE2_PATH`)
- [`docs/PRIVACY.md`](docs/PRIVACY.md) — GDPR-oriented notice templates for client websites
- Scheduler ops heartbeat and `GET /health/ops` (loopback) for Docker healthchecks
- `AnalyticsUserAgent` helper for browser/OS parsing
- Tests: analytics geo, analyze flow, domain store SSRF, browser UA, ops health

### Changed

- Visitor hashes use truncated IPs before HMAC (no raw IP stored)
- Docker healthcheck uses `/health/ops` instead of `/login`
- Queue jobs define `$tries` and `$timeout` (`CheckUrlJob`, `CheckDomainJob`, `PerformSpectoraAudit`, `SendMonthlyReportsJob`)
- README, RUNBOOK, and SECURITY expanded (queue worker required, geo setup, scaling limits)

### Fixed

- Manual **Analyze** button: correct `CheckDomainJob::dispatchSync($domain, synchronous: true)` usage
- Analytics browser detection: Edge and Opera no longer counted as Chrome
- `spectora:setup` sets `is_admin` after create (not mass-assignable)
- Monitored URL tests use resolvable hosts (`example.com`) for SSRF checks in CI
- Removed ineffective `set_time_limit()` in `PerformSpectoraAudit` (CLI queue context)

---

## [1.0.0] - 2026-05-30

Major self-hosted overhaul (May 2026). Treat upgrades from pre-2026 installs as a **schema migration** — see README “Updates from Older Versions”.

### Added

- Central `SecurityService` (SSRF DNS pinning, redirect re-validation, pinned outbound HTTP)
- `MonitoredUrlHelper` for same-host URL and sitemap validation
- `DomainAlertService` — downtime email + optional Web Push to domain owner and all admins
- `DomainMonitoringController` split from `DomainController`
- Async uptime checks via queue (`chunkById(50)` scheduler, `CheckDomainJob` → `CheckUrlJob`)
- Synchronous path for manual dashboard **Analyze**
- Domain notes with author (`content`, `user_id`, schema repair migration)
- `docker-compose.prod.yml`, `scripts/backup-sqlite.sh`, `docs/RUNBOOK.md`, `docs/LEGACY_SCHEMA.md`
- `SECURITY.md`, `CONTRIBUTING.md`
- CI: PHPUnit, Pint, `composer audit`, `npm audit`, Vite build
- IDOR and monitoring URL feature tests

### Changed

- Laravel 12.61+ (security patches)
- `is_admin` removed from `User::$fillable`
- Route throttles on sensitive endpoints
- README aligned with implementation; post-overhaul testing notice

### Fixed

- `notify_sent` mass assignment on domains
- Registration disabled by default; admin via `spectora:setup`

### Security

- SSL socket checks use validated IPs + SNI (`peer_name`)
- Alert issue text sanitization in `DomainAlertService`
- Analytics: origin validation, daily HMAC visitor hashes, rate-limited `/api/sync`

---

## Older history

Installations before the May 2026 SaaS removal and schema refactor may need `php artisan migrate --force` or a documented fresh setup. See git history prior to `1.0.0` for legacy SaaS-related changes.
