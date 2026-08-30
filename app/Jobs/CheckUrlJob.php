<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Models\MonitoredUrl;
use App\SpectoraEngine\SpectoraEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public int $backoff = 10;

    public function __construct(
        public Domain $domain,
        public ?string $url = null,
        public ?MonitoredUrl $monitoredUrl = null
    ) {
        $this->url = $url ?? ($monitoredUrl ? $monitoredUrl->url : $domain->url);
    }

    public function handle(?SpectoraEngine $engine = null): void
    {
        ($engine ?? app(SpectoraEngine::class))->probe($this->domain, $this->monitoredUrl, $this->url);
    }
}
