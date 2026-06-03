# Pre-release staging checklist

Run on a **staging** instance (copy of production data volume optional) before tagging a release.

1. **Migrations** — `php artisan migrate --force` completes without errors.
2. **Scheduler** — `supervisorctl status scheduler` is `RUNNING`; `GET /health/ops` on loopback returns 200 (Docker: `curl -f http://127.0.0.1:8080/health/ops`).
3. **Queue** — `queue-worker` is `RUNNING`; dispatch a manual check (`php artisan tinker` → `CheckUrlJob::dispatchSync($domain)`) and confirm `checks_history` updates.
4. **Alerts** — Trigger a failing main URL and a failing monitored sub-URL; confirm e-mail subject includes the checked URL for sub-paths.
5. **Smoke UI** — Log in, open a domain dashboard, run **Analyze**, confirm analytics/geo settings save.

Document the date and operator in your deployment notes when all five pass.
