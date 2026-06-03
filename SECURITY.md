# Security Policy

## Supported Versions

Security fixes are applied to the latest release on the `main` branch.

## Reporting a Vulnerability

Please **do not** open public GitHub issues for security vulnerabilities.

Report privately to the maintainers via GitHub Security Advisories on
[Everlite/Spectora-Website-Monitoring-Suite](https://github.com/Everlite/Spectora-Website-Monitoring-Suite/security/advisories/new)
or contact the repository owner directly.

We aim to acknowledge reports within a few business days.

## Self-Hosted Operators

- Keep `composer update` and `npm audit` current.
- Set `APP_DEBUG=false` and `SESSION_ENCRYPT=true` in production.
- Run Spectora behind HTTPS with `TRUSTED_PROXIES` configured when using a reverse proxy.
- Never commit `.env` or real `APP_KEY` values to version control; rotate the key if it was ever exposed.
- In production: `APP_DEBUG=false`, `SESSION_ENCRYPT=true`, and ensure the **queue worker** and **scheduler** are running (see [docs/RUNBOOK.md](docs/RUNBOOK.md)).
