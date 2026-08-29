<?php

namespace App\Jobs;

use App\Models\ChecksHistory;
use App\Models\Domain;
use App\Services\MonitoringFilterService;
use App\Services\SecurityService;
use GuzzleHttp\TransferStats;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class PerformSpectoraAudit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 180;

    public function __construct(public Domain $domain) {}

    public function handle(): void
    {
        $filterService = app(MonitoringFilterService::class);

        $url = $this->domain->url;
        if (! str_starts_with($url, 'http')) {
            $url = 'https://'.$url;
        }

        // 0. Pre-check Filter (Excludes, Robots.txt)
        $filter = $filterService->shouldCheck($this->domain, $url);
        if (! $filter['should_check']) {
            Log::info("Skipping audit for {$url}: {$filter['reason']}");

            return;
        }

        // 0.5 SSRF Protection
        if (! SecurityService::resolve()->isSafeUrl($url)) {
            Log::warning("SSRF Protection: Blocked prohibited audit for {$url}");

            return;
        }

        try {
            $auditEngine = app(\App\SpectoraEngine\Audit\SpectoraAuditEngine::class);
            $auditResult = $auditEngine->audit($this->domain);

            // 3. Save Score & Details
            $this->domain->pagespeed_score_desktop = $auditResult->score; // Spectora Multi-Factor Score
            $this->domain->last_pagespeed_details = $auditResult->findings;
            $this->domain->touch();
            $this->domain->save();

            // Save to History
            ChecksHistory::create([
                'domain_id' => $this->domain->id,
                'pagespeed_score_desktop' => $auditResult->score,
                'created_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error("Spectora Audit Exception for {$url}: ".$e->getMessage());
        } finally {
            SecurityService::clearHostIpCache();
        }
    }
}
