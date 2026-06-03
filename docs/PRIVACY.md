# Spectora Analytics — Privacy & GDPR Notes

Spectora provides **optional, cookie-free, first-party analytics** for websites you monitor. This document helps agencies and shop owners describe the processing and configure Spectora responsibly.

> **Not legal advice.** Website operators remain responsible for their privacy policy, legal basis, and any consent requirements alongside other tools (e.g. ad cookies, payment providers).

## What is collected

| Data | Purpose | Stored in DB |
|------|---------|--------------|
| Page URL / path | Popular pages | Yes (path + full URL) |
| Referrer domain | Traffic sources (SEO) | Yes |
| Device type | Mobile / tablet / desktop (viewport width) | Yes |
| Browser / OS (coarse) | Aggregate stats | Yes |
| Country / region / city | Local SEO, shops, trades | Yes (per domain setting) |
| Daily visitor hash | Unique visitors per day | Yes (HMAC, no cross-day ID) |
| Raw IP address | — | **No** |

Geo is derived **once per pageview** from the client IP or from your reverse proxy (e.g. Cloudflare). The IP is **not** written to the database.

## What is not collected

- No analytics cookies
- No cross-site tracking or advertising IDs
- No fingerprinting
- No sale of data to third parties (self-hosted)

## Configuration

Per domain: **Analytics → Visitor location**

- **Country + region + city** — recommended for online shops and local businesses
- **Country only** — stricter minimization
- **Off** — no geo fields stored

### Geo data sources (server-side)

1. **Cloudflare (recommended):** Set `TRUSTED_PROXIES` on Spectora so Laravel trusts `CF-IPCountry`, `CF-IPRegion`, and `CF-IPCity`.
2. **GeoLite2 (without Cloudflare):** Download [GeoLite2-City](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data) (free MaxMind account), place `GeoLite2-City.mmdb` at `storage/app/geoip/GeoLite2-City.mmdb` or set `ANALYTICS_GEOLITE2_PATH`.

## Retention

Analytics rows are pruned automatically after **180 days** (`model:prune`).

## Suggested text for client privacy policies (German)

You may adapt this paragraph for shop / Handwerk websites:

> Wir nutzen auf dieser Website eine **selbst gehostete, cookie-freie Reichweitenstatistik** (Spectora). Dabei werden Seitenaufrufe, ungefähre Herkunft (Land/Region/Stadt), Gerätetyp (Mobil/Tablet/Desktop) und Verweisquelle (z. B. Suchmaschine) verarbeitet, um unsere Website und lokales Marketing zu verbessern. Es werden **keine** Marketing-Cookies gesetzt und **keine** vollständigen IP-Adressen gespeichert. Rechtsgrundlage: berechtigtes Interesse (Art. 6 Abs. 1 lit. f DSGVO) unter Abwägung Ihrer Interessen; Speicherdauer ca. 6 Monate. Sie können der Verarbeitung widersprechen (Kontakt siehe Impressum).

## Suggested text (English)

> We use **self-hosted, cookie-free analytics** (Spectora) to measure page views, approximate location (country/region/city), device type, and referrer for website improvement. No advertising cookies are set and full IP addresses are not stored. Legal basis: legitimate interest (Art. 6(1)(f) GDPR), retention approx. 6 months. You may object via the contact details in our legal notice.

## Marketing claims (honest wording)

**Recommended:**

- Cookie-free, self-hosted analytics — alternative to Google Analytics  
- Privacy-by-design: minimal fields, no raw IP storage, fixed retention  
- GDPR-oriented when configured with a proper privacy notice on the client site  

**Avoid:**

- “100% GDPR compliant” or “fully anonymous” (city-level geo is still personal data in some cases)  
- “No privacy policy needed”

## Operator checklist

- [ ] HTTPS for Spectora (`APP_URL`) and client sites  
- [ ] `TRUSTED_PROXIES` when behind Cloudflare/nginx  
- [ ] GeoLite2 file OR Cloudflare headers for city/country data  
- [ ] Privacy policy updated on each client website  
- [ ] Geo precision set per domain (`off` / `country` / `city`)
