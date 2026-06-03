<?php

use App\Jobs\CheckDomainJob;
use App\Jobs\PerformSpectoraAudit;
use App\Jobs\SendMonthlyReportsJob;
use App\Models\Domain;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

// Heartbeat for Docker /health/ops (proves cron + scheduler are alive)
Schedule::call(function () {
    Cache::put('spectora:ops:heartbeat', now(), 3600);
})->everyMinute()->name('ops_heartbeat');

// Uptime, SSL, Watchdog — every 15 minutes (chunked; checks run async via queue)
Schedule::call(function () {
    Cache::put('spectora:ops:heartbeat', now(), 3600);

    Domain::query()->orderBy('id')->chunkById(50, function ($domains) {
        foreach ($domains as $domain) {
            CheckDomainJob::dispatch($domain);
        }
    });
})->everyFifteenMinutes()->name('check_domains')->withoutOverlapping();

// Spectora Audit (heuristic score) — hourly
Schedule::call(function () {
    Domain::query()->orderBy('id')->chunkById(50, function ($domains) {
        foreach ($domains as $domain) {
            PerformSpectoraAudit::dispatch($domain);
        }
    });
})->hourly()->name('spectora_audit')->withoutOverlapping();

// Prune checks_history older than 90 days (ChecksHistory::prunable)
Schedule::command('model:prune')->daily();

// Agency digest email — 1st of month at 08:00 (no PDF; see README)
Schedule::job(new SendMonthlyReportsJob)->monthlyOn(1, '08:00');
