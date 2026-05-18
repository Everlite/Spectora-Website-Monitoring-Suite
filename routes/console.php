<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Domain;
use App\Jobs\CheckDomainJob;

// Uptime, SSL, Watchdog — every 15 minutes
Schedule::call(function () {
    foreach (Domain::all() as $domain) {
        CheckDomainJob::dispatch($domain);
    }
})->everyFifteenMinutes()->name('check_domains')->withoutOverlapping();

// Spectora Audit (heuristic score) — hourly
Schedule::call(function () {
    foreach (Domain::all() as $domain) {
        \App\Jobs\PerformSpectoraAudit::dispatch($domain);
    }
})->hourly()->name('spectora_audit')->withoutOverlapping();

// Prune checks_history older than 90 days (ChecksHistory::prunable)
Schedule::command('model:prune')->daily();

// Agency digest email — 1st of month at 08:00 (no PDF; see README)
Schedule::job(new \App\Jobs\SendMonthlyReportsJob)->monthlyOn(1, '08:00');
