# Changelog

## [Unreleased] — Major Overhaul (May 2026)

### Analytics (privacy geo)

- Country, region, and city storage with per-domain precision (`off` / `country` / `city`)
- Top Countries / Top Cities in analytics UI; Cloudflare or GeoLite2-City resolution
- Truncated IP before visitor hash; `docs/PRIVACY.md` for client notice templates
- Optional dependency `geoip2/geoip2` for local MaxMind database

Spectora received a large stability, security, and maintainability refresh. Treat this line as a **post-overhaul testing phase**: automated CI covers core flows; validate on a staging instance before production upgrades.

### Security

- Central SSRF protection with DNS pinning and redirect validation
- Monitored URL and sitemap host validation
- Domain alerts to owners and admins (email + optional Web Push)
- User policies, route throttles, Laravel 12.61+ (CVE fixes)
- `composer audit` and `npm audit` in CI

### Reliability

- Scheduler uses `chunkById`; uptime checks run asynchronously via queue
- Manual “Analyze” still runs checks synchronously for immediate feedback
- `notify_sent` and domain notes schema fixes

### Architecture

- Split `DomainMonitoringController` from `DomainController`
- `DomainAlertService`, `MonitoredUrlHelper`
- Production `docker-compose.prod.yml` and SQLite backup script

### Documentation

- `SECURITY.md`, `CONTRIBUTING.md`, `docs/RUNBOOK.md`
- README aligned with implementation (~99%)

### Tests

- Expanded PHPUnit coverage (authorization, jobs, alerts, monitoring URLs)
