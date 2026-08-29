<div align="center">
  <img width="256" height="209" alt="spectora_logo" src="https://github.com/user-attachments/assets/2166df18-7009-466d-99b7-de29dae8bb66" />
  <h1>Spectora: Private Self-Hosted Website Monitoring Suite</h1>
  
  [![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
  [![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=for-the-badge&logo=php)](https://php.net)
  [![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?style=for-the-badge&logo=docker)](https://www.docker.com)
  [![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](LICENSE)

  <p align="center">
    <strong>Powered by the proprietary Spectora Engine™ · Cyber-Glassmorphic UI · Self-Hosted &amp; Private by Design</strong><br>
    Laravel 12 · Docker · Heuristic Watchdog · Multi-Factor Audits · Zero-Cookie Pulse Telemetry · Discord &amp; Slack Webhooks · Multi-Channel Recovery Alerts
  </p>
</div>

---

> [!IMPORTANT]
> **Spectora Engine Architecture & Cyber-Glassmorphic Redesign (2026)**
> Spectora is now powered by the custom **Spectora Engine** ecosystem:
> 1. **Spectora Audit Engine:** Multi-factor scoring matrix (0–100, Grade A+ to F) evaluating TTFB, payload size, H1 hierarchy, SEO snippets, image alt tags, and security headers.
> 2. **Spectora Watchdog Engine:** Heuristic malware scanner detecting obfuscated JS (`eval`, `String.fromCharCode`), CJK SEO title spam, cloaking, and hidden iframes.
> 3. **Spectora Pulse Telemetry:** Privacy-first, zero-cookie telemetry kernel with SPA History API auto-tracking and custom conversion tracking (`window.spectora.track`).
> 4. **Incident State Machine & Multi-Channel Alerting:** Intelligent outage & recovery lifecycle with direct Discord and Slack Webhooks, E-Mail, and Web Push.
> 5. **Cyber-Glassmorphic UI:** High-end dark obsidian interface with neon cyber accents, live pulsing radars, and zero N+1 database queries.

**Spectora** is a premium, open-source, **Private by Design Self-Hosted Website Monitoring Suite** custom-tailored for freelancers and modern digital agencies. It allows you to monitor all your client websites (uptime, SSL, security keywords, deep audits, telemetry, and threat detection) from one central command center on your own server—completely free of recurring SaaS subscriptions.

---

## 🏛️ The Proprietary Spectora Engine Architecture

```mermaid
flowchart TD
    subgraph ClientSite [Client Website]
        SP["sp-pulse.js / sp-core.js (< 1KB)"]
        SPA["SPA History API Tracker"]
        CE["Custom Event API (window.spectora.track)"]
    end

    subgraph SpectoraCore [Spectora Engine Kernel]
        PULSE["Spectora Pulse Ingest\n(HMAC-SHA256 Daily Subkeys + GDPR)"]
        AUDIT["Spectora Audit Engine\n(Multi-Factor Index: 0-100, Grade A+ to F)"]
        WATCHDOG["Spectora Watchdog Engine\n(Obfuscated JS, CJK SEO Spam, Cloaking)"]
        STATE["Incident State Machine\n(Soft-Fail, Outage, Recovery Lifecycle)"]
    end

    subgraph Channels [Multi-Channel Alerts]
        MAIL["E-Mail (Downtime & Recovery)"]
        DISCORD["Discord Webhooks"]
        SLACK["Slack Webhooks"]
        WEBPUSH["Web Push Notifications"]
    end

    ClientSite -->|Telemetry Beacons| PULSE
    AUDIT -->|Multi-Factor Scores & Grades| Dashboard
    WATCHDOG -->|Heuristic Threat Assessments| STATE
    STATE -->|Auto-Dispatch Outages & Recoveries| Channels
```

---

## ⚡ Core Modules & Features

### 1. Spectora Audit Engine (`App\SpectoraEngine\Audit`)
* **Multi-Factor Scoring Matrix (0–100 Index & Letter Grades A+ to F):**
  * **Performance:** Server TTFB (Time-To-First-Byte) and HTML payload size optimization.
  * **Structure & SEO:** Single H1 hierarchy enforcement, `<title>` snippet validation, and `<meta name="description">` audits.
  * **Accessibility:** Automatic verification of image `alt` attributes.
  * **Security:** HTTPS enforcement, Strict-Transport-Security (HSTS), X-Frame-Options framing defenses, and X-Content-Type-Options.
* Replaces heavy external dependencies (e.g. Google Lighthouse/Chromium) with instant, lightweight local audits.

### 2. Spectora Watchdog Engine (`App\SpectoraEngine\Watchdog`)
* Deep heuristic malware, cloaking, and defacement detection running with zero latency overhead during uptime checks:
  * **Obfuscated Payloads:** Detects `eval(String.fromCharCode(...))`, `document.write(unescape(...))`, and browser cryptominers (CoinHive, CryptoLoot).
  * **SEO Spam Hijacking:** Detects Japanese Keyword Hack (CJK unicode spam) and common hack titles (casino, payday, pharma).
  * **Black-Hat Cloaking:** Flags text hidden via `display:none`, `visibility:hidden`, and extreme negative text-indents.
  * **Malicious Iframes:** Flags hidden 0px/1px iframes used for credential harvesting or drive-by downloads.
  * **Defacement Redirects:** Catches client-side meta-refresh redirect attacks.

### 3. Spectora Pulse Telemetry (`App\SpectoraEngine\Pulse`)
* **Zero-Cookie Privacy Telemetry:**
  * Ultra-lightweight `< 1KB` tracking kernel ([public/js/sp-pulse.js](public/js/sp-pulse.js)) and backward-compatible `sp-core.js`.
  * **SPA Navigation Support:** Automatically tracks route changes in Single Page Applications (React, Vue, Next.js, Nuxt) via the History API (`pushState` / `popstate`).
  * **Custom Conversion Events:** Allows client websites to track conversion actions:
    ```javascript
    window.spectora.track('lead_form_submitted', { plan: 'enterprise' });
    ```
  * **GDPR-Compliant Daily Visitor Hashing:** Generates rotating `HMAC-SHA256` visitor hashes with truncated IPs and daily rotating server subkeys. No raw IP addresses or persistent identifiers are ever stored.

### 4. Incident State Machine & Multi-Channel Alerting (`App\SpectoraEngine\Incidents`)
* **Incident Lifecycle Management:** Tracks consecutive failures and prevents flapping/alert storms.
* **Instant Outage Alerts:** Triggered on confirmed failures across configured channels.
* **Automated Recovery Alerts:** Automatically dispatches a resolution alert as soon as the site recovers healthy HTTP responses.
* **Multi-Channel Dispatcher:**
  * **Discord Webhooks:** Rich visual embeds with error details and direct dashboard jump links.
  * **Slack Webhooks:** Formatted incident cards for agency communication channels.
  * **E-Mail Alerts:** HTML downtime warnings and recovery digests.
  * **Web Push Notifications:** Real-time desktop and mobile browser push notifications via VAPID.

### 5. High-Performance Batch Architecture
* **Zero N+1 Query Overhead:** Dashboard and overview metrics use batched subquery aggregation for 30-day uptimes, average response times, and daily visitors.
* **Composite Indexes:** Optimized SQLite queries with `(domain_id, created_at)` and `(domain_id, response_time, created_at)` index layers.

---

## 🎨 Cyber-Glassmorphic UI ("WOW Factor")

The user interface has been completely redesigned with a cutting-edge **Cyber-Glassmorphism** aesthetic:
* **Dark Obsidian Foundations:** Deep slate and obsidian backgrounds (`#0B0F17`, `#070B12`, `#131B2E`) with backdrop blur effects.
* **Neon Glow Accents:** Electric Cyan (`#00F2FE`), Emerald Radar (`#10B981`), Rose Outage (`#F43F5E`), and Violet Telemetry (`#8B5CF6`).
* **Live Pulsing Radars:** Real-time animated status beacons for active domain probe cycles.
* **Spectora Grade Badges:** Visual Grade pills (`Grade A+`, `Grade B`, etc.) for every monitored target.
* **Modular Blade Architecture:** Componentized layout using `<x-spectora.global-metrics>`, `<x-spectora.domain-card>`, `<x-spectora.watchdog-modal>`, `<x-spectora.notes-modal>`, and `<x-spectora.delete-modal>`.

---

## 🚀 Quick Start with Docker

### Prerequisites
* Docker & Docker Compose
* ~1 GB RAM
* SMTP credentials (recommended for email alerts and monthly digests)

### 1. Start the Container
Clone the repository and spin up the environment:
```bash
git clone https://github.com/Everlite/Spectora-Website-Monitoring-Suite.git
cd Spectora-Website-Monitoring-Suite
docker compose up -d --build
```
On first launch, Spectora automatically prepares your environment, copies `.env`, runs database migrations, and configures storage symlinks.

### 2. Run the Interactive Admin Setup
Initialize your primary administrator account securely via the CLI:
```bash
docker compose exec app php artisan spectora:setup
```
Enter your **first name, last name, email, and password** when prompted.

Navigate to **http://localhost:8000**, log in, and explore your new command center!

---

## ⚙️ Configuration (.env)

| Variable | Purpose |
| :--- | :--- |
| `APP_URL` | Public URL of your instance (e.g. `https://spectora.your-agency.com`). |
| `TRUSTED_PROXIES` | Set to `*` or proxy IPs behind reverse proxies (Nginx, Traefik, Caddy, Cloudflare). |
| `SPECTORA_REGISTRATION_ENABLED` | `false` (default) — disables public sign-up for agency privacy. |
| `DB_DATABASE` | SQLite database path (default in Docker: `/var/www/html/storage/database.sqlite`). |
| `DB_BUSY_TIMEOUT` | SQLite timeout tuning (default: `5000` ms) for concurrent queue writes. |
| `QUEUE_CONNECTION` | `database` (default) — executed automatically via Docker Supervisor. |
| `MAIL_*` | SMTP configuration for email alerts, recovery notifications, and monthly digests. |
| `VAPID_PUBLIC_KEY` / `VAPID_PRIVATE_KEY` | Web Push notification keys (subscribe directly from the UI). |
| `SESSION_ENCRYPT` | Set to `true` in production to encrypt session cookies at rest. |

---

## 📡 Pulse Client Tracking Code

To enable privacy-first telemetry on a client website, embed this snippet before `</body>`:

```html
<!-- Spectora Pulse Telemetry Kernel -->
<script defer src="https://spectora.yourdomain.com/js/sp-pulse.js" data-domain="YOUR_DOMAIN_UUID"></script>
```

### Tracking Custom Events
```javascript
// Track button clicks, form submissions, or purchases
window.spectora.track('purchase_completed', { value: 49.00, currency: 'EUR' });
```

---

## 🧪 Testing

Spectora includes a complete test suite covering the engine kernels, security filters, and incident workflows:

```bash
# Run unit and feature tests
docker compose exec app php artisan test
```

Unit test coverage:
* `SpectoraAuditEngineTest`: Multi-factor scoring matrix, A+ grade determination, and penalties.
* `SpectoraWatchdogEngineTest`: Detection of obfuscated payloads, CJK SEO spam, and cloaking.
* `PulseIngestEngineTest`: Daily visitor hash rotation, origin checks, and event ingestion.
* `IncidentStateMachineTest`: Outage alert triggering and automated recovery dispatch.

---

## 📄 License & Credits

Spectora is open-source software licensed under the **[MIT License](LICENSE)**.

Built with passion for freelancers and agencies that value privacy, elegance, and complete data ownership.

*Created and maintained by [Everlite](https://github.com/Everlite).*
