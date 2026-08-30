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

**Releases:** [GitHub Releases](https://github.com/Everlite/Spectora-Website-Monitoring-Suite/releases) — latest tag: **v0.2.2**.

---

## [Unreleased]

### Added

- **Proprietary Spectora Engine Core (`App\SpectoraEngine`)**:
  - `SpectoraAuditEngine`: Multi-factor scoring matrix (0–100, Grade A+ to F) evaluating TTFB, size, H1 structure, SEO snippets, alt tags, and security headers.
  - `SpectoraWatchdogEngine`: Heuristic malware scanner detecting eval-obfuscated JS (`eval`, `String.fromCharCode`), CJK SEO title spam, cloaking, and malicious hidden iframes.
  - `PulseIngestEngine`: Zero-cookie telemetry with rotating HMAC-SHA256 visitor subkeys, origin validation, and custom event tracking.
  - `IncidentStateMachine`: Intelligent outage & recovery lifecycle manager with automated healing detection.
  - `AlertDispatcher`: Multi-channel notification dispatcher supporting **Discord Webhooks**, **Slack Webhooks**, Email, and Web Push.
- **Client Tracking Kernels**:
  - `public/js/sp-pulse.js`: < 1KB tracking script supporting SPA History API route tracking (`pushState`/`popstate`) and `window.spectora.track()` custom events.
- Modular Blade components: `<x-spectora.global-metrics>`, `<x-spectora.domain-card>`, `<x-spectora.add-domain-modal>`, `<x-spectora.watchdog-modal>`, `<x-spectora.notes-modal>`, and `<x-spectora.delete-modal>`.
- Webhook URL configuration in User Settings.
- **Database & Performance**:
  - Migration `2026_06_15_000000_upgrade_spectora_engine_schema.php` with `webhook_url` support and composite indexes on `checks_history` and `analytics_visits`.
  - Batch aggregation in `DashboardController` eliminating all N+1 query overhead.
- **Unit Tests**:
  - `SpectoraAuditEngineTest`, `SpectoraWatchdogEngineTest`, `PulseIngestEngineTest`, `IncidentStateMachineTest`, and `SpectoraEngineProbeTest`.

### Changed

- `SpectoraEngine::probe()` is the single outbound check cycle (filter, one HTTP fetch, keywords, watchdog on the same body, persist, incidents). `CheckUrlJob` is a queue wrapper.
- Domain page is a Pulse report first (visitors, pageviews, pages, sources, devices, geo), health as a one-line strip, Engine-Bericht below. Website list leads with visitors, not latency.
- Shell is a top masthead with spectrum mark (no sidebar). Ledger typography (Instrument Serif, zero radius, signal lime). Score is labeled Dokument-Score — HTML Spectora fetched, not Lighthouse.
- Audit findings are German, use `label`/`success` (not index `0` + red X on passes), and render as an Engine-Bericht table on the cockpit.
- Auth, shell, and settings use Studio tokens and the Spectora mark. Pulse snippet is advertised as `sp-pulse.js`.
- README rewritten to match the Engine kernel, fleet/cockpit UI, and Docker deploy — no v2.4 / proprietary brochure copy.

### Removed

- Duplicate `WatchdogService` (replaced by `SpectoraWatchdogEngine`).
- Domain overhaul-banner and the 5-tab controller.

---

## [0.2.2] - 2026-06-21

### Security

- Dependabot: `form-data` → **4.0.6** (CRLF injection in multipart field names — alert #11)

### Changed

- CI: GitHub Actions v5 (`checkout`, `cache`, `setup-node`), Node **24**, `key:generate --force` (no production prompt)
- npm: pin `form-data` override, approve `esbuild` install scripts, `.npmrc` (`fund=false`)

---

## [0.2.1] - 2026-05-30

### Security

- Dependabot: `laravel/framework` → **12.62.0** (signed URL path confusion, GHSA #5)
- Dependabot: `web-token/jwt-library` → **4.1.7** (PBES2 DoS, algorithm confusion, RSA1_5 oracle, ChaCha20Poly1305 — alerts #7–#10)
- Dependabot: `vite` → **7.3.5** (dev-server `fs.deny` bypass + `launch-editor` UNC/NTLM — alerts #4, #6)

### Added

- Domain dashboard refactor: partials under `resources/views/domains/dashboard/partials/`, header/overhaul banner components; prominent **untested overhaul** notice on the dashboard
- Sub-URL downtime alerts (`notify_sent` on `monitored_urls`, per-URL e-mail subject)
- `docs/STAGING_CHECKLIST.md` pre-release gate
- Tests: monitored URL alerts, `PerformSpectoraAudit`, `WatchdogService` spam detection

### Changed

- README / package description: honest positioning (GDPR-oriented, no Google Analytics, cookie-free analytics — not "no tracking" / "no third parties")
- `SecurityService` instance singleton with per-job `clearHostIpCache()` (queue-safe DNS cache)
- Docker: Apache on **8080** as `www-data`, `schedule:work` instead of root cron
- RUNBOOK: scheduler/health port 8080

### Removed

- Maintainer-only files from the public tree (`.github/GH_COMMANDS.md`, `.github/REPO_DESCRIPTION.txt`, `.github/RELEASE_v0.2.0.md`, `dashboard/partials/README.md`, `_ide_stubs.php`, obsolete `docker/laravel.cron`)

---

## [0.2.0] - 2026-06-04

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
