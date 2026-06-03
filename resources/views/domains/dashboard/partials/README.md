# Domain dashboard partials

The domain dashboard (`resources/views/domains/dashboard.blade.php`) is composed from this folder.

| File | Tab / section |
|------|----------------|
| `styles.blade.php` | Grid layout CSS |
| `flash-and-tabs.blade.php` | Session flash + tab navigation |
| `tab-overview.blade.php` | Overview (stats, charts, audit) |
| `security-modal.blade.php` | Watchdog security modal (Alpine) |
| `tab-monitoring.blade.php` | Monitoring settings + URL picker |
| `tab-analytics.blade.php` | Analytics tables + device chart |
| `tab-history.blade.php` | Analysis + event log |
| `tab-notes.blade.php` | Domain notes |
| `scripts.blade.php` | Chart.js + Alpine helpers |

Header and overhaul banner: `resources/views/components/dashboard/`.

**Status (May 2026):** Large UI refactor — treat as **beta** until manually tested (see overhaul banner on the page).
