<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\ChecksHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckDomainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $domain;

    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    public function handle(): void
    {
        // 1. Check Main Domain URL
        CheckUrlJob::dispatch($this->domain);

        // 2. Check all active sub-URLs
        $activeUrls = $this->domain->monitoredUrls()->where('is_active', true)->get();
        foreach ($activeUrls as $monitoredUrl) {
            CheckUrlJob::dispatch($this->domain, null, $monitoredUrl);
        }
    }
}
