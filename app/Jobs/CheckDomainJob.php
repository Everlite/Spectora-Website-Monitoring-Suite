<?php

namespace App\Jobs;

use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckDomainJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Domain $domain,
        public bool $synchronous = false,
    ) {}

    public function handle(): void
    {
        $dispatch = $this->synchronous
            ? fn (Domain $domain, ?string $url = null, $monitoredUrl = null) => CheckUrlJob::dispatchSync($domain, $url, $monitoredUrl)
            : fn (Domain $domain, ?string $url = null, $monitoredUrl = null) => CheckUrlJob::dispatch($domain, $url, $monitoredUrl);

        $dispatch($this->domain);

        $this->domain->monitoredUrls()
            ->where('is_active', true)
            ->each(fn ($monitoredUrl) => $dispatch($this->domain, null, $monitoredUrl));
    }
}
