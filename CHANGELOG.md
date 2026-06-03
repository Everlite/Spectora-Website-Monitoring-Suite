# Changelog

All notable changes to [Spectora](https://github.com/Everlite/Spectora-Website-Monitoring-Suite) are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). Version numbers follow [Semantic Versioning](https://semver.org/):

- **`0.x.y`** — early stable releases (current line; first public release was **v0.1.0**)
- **`1.0.0`** — reserved for a future API/stable milestone, not used yet

**How we maintain it**

- User-facing changes on `main` go under **`[Unreleased]`** while in progress.
- When shipping a GitHub release, move those entries into **`[0.x.y] - YYYY-MM-DD`**, tag `v0.x.y`, and clear `[Unreleased]`.
- Groups: `Added`, `Changed`, `Fixed`, `Security`, `Deprecated`, `Removed`.
- See [README.md](README.md) when upgrading between tagged versions.

**Releases:** [GitHub Releases](https://github.com/Everlite/Spectora-Website-Monitoring-Suite/releases) — latest tag: **v0.1.0** (first stable). `main` is ahead; next tag is planned as **v0.2.0**.

---

## [Unreleased]

### Planned

- Sub-URL downtime alerts (not only the main domain URL)
- Blade component split for `dashboard.blade.php`
- Optional Docker hardening (Apache non-root)

---

## [0.2.0] - 2026-06-04

> **Not tagged yet.** Summarizes all changes on `main` since **v0.1.0**. After validation, tag `v0.2.0` and publish the GitHub release.

### Added

- Privacy-friendly analytics geo: country, region, and city (per-domain precision: `off` / `country` / `city`)
- Top Countries / Top Cities in the analytics UI
- Geo via Cloudflare headers (`TRUSTED_PROXIES`) or GeoLite2-City (`geoip2/geoip2`, `ANALYTICS_GEOLITE2_PATH`)
- [`docs/PRIVACY.md`](docs/PRIVACY.md) for client privacy notice templates
- Scheduler ops heartbeat and `GET /health/ops` (loopback) for Docker healthchecks
- `CHANGELOG.md` (this file) and maintenance notes in README / CONTRIBUTING
- `DomainMonitoringController`, `DomainAlertService`, `MonitoredUrlHelper`
- Async uptime checks (scheduler `chunkById`, queue jobs)
- Domain notes with author; `docker-compose.prod.yml`, backup script, `docs/RUNBOOK.md`, `docs/LEGACY_SCHEMA.md`
- `SECURITY.md`, `CONTRIBUTING.md`; expanded CI and feature tests

### Changed

- Major stability/security pass (May–June 2026): SSRF connect-time pinning, alert routing, authz tests
- Visitor hashes use truncated IPs before HMAC (no raw IP stored)
- Docker healthcheck uses `/health/ops` instead of `/login`
- Queue jobs define `$tries` and `$timeout`
- README, RUNBOOK, SECURITY: queue worker required, geo setup, ~50 domain scope
- Laravel 12.61+; `is_admin` removed from mass assignment

### Fixed

- Manual **Analyze** button: `CheckDomainJob::dispatchSync($domain, synchronous: true)`
- Analytics browser detection (Edge/Opera before Chrome)
- `spectora:setup` sets `is_admin` after create
- Monitored URL / CI tests use resolvable hosts (`example.com`)
- `notify_sent` fillable; registration test and noindex crawler CI fixes

### Security

- Central `SecurityService` (DNS pinning, redirect checks, pinned HTTP)
- SSL socket checks with validated IP + SNI
- Monitored URL / sitemap same-host validation
- Alert sanitization; analytics origin checks and rate limits

---

## [0.1.0] - 2026-05-23

First stable release on GitHub (**`v0.1.0`** — *first stable release*).

### Added

- Self-hosted website monitoring (uptime, SSL, keywords, lightweight Spectora score)
- Cookie-free analytics snippet (`/js/sp-core.js`, `/api/sync`)
- Docker Compose deployment (Apache, cron, queue worker via Supervisor)
- SQLite storage with WAL-friendly defaults
- Private-by-design defaults: registration off, `spectora:setup` for first admin
- PDF reports, monthly digest mail, domain dashboard (Alpine.js + Tailwind)
- Core SSRF protections for outbound checks

### Security

- Pre-release audit fixes bundled for v0.1.0 tag (`security: fix 3 findings from audit (v0.1.0 prep)`)

---

## Before v0.1.0

History includes the SaaS-to-self-hosted transition (March 2026) and earlier private development. No changelog was kept until **0.2.0** documentation; use `git log` before tag `v0.1.0` for details.
