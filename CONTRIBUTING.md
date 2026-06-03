# Contributing to Spectora

Thank you for helping improve Spectora.

## Development Setup

```bash
composer setup
php artisan spectora:setup
composer dev
```

Requires PHP 8.2+ with `pdo_sqlite`, and Node.js for Vite.

## Pull Requests

1. Branch from `main`.
2. Add or update tests for behavior changes.
3. Update **[CHANGELOG.md](CHANGELOG.md)** under `[Unreleased]` for anything operators or users should know (features, fixes, security, config, migrations).
4. Run before opening a PR:

```bash
composer test
vendor/bin/pint
npm ci && npm run build
composer audit
npm audit
```

5. Keep changes focused; match existing Laravel and Blade conventions.

## Code Style

PHP formatting uses [Laravel Pint](https://laravel.com/docs/pint).

## Operations

See [docs/RUNBOOK.md](docs/RUNBOOK.md) for backups, production Compose, and upgrades.

## Security

See [SECURITY.md](SECURITY.md).
