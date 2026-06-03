<?php

namespace Tests\Unit;

use App\Jobs\CheckDomainJob;
use App\Jobs\CheckUrlJob;
use App\Models\Domain;
use App\Models\MonitoredUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CheckDomainJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduled_run_dispatches_check_url_jobs_to_queue(): void
    {
        Queue::fake();

        $domain = Domain::factory()->create();
        MonitoredUrl::create([
            'domain_id' => $domain->id,
            'url' => $domain->url.'/about',
            'is_active' => true,
        ]);

        (new CheckDomainJob($domain))->handle();

        Queue::assertPushed(CheckUrlJob::class, 2);
    }

    public function test_manual_analyze_uses_synchronous_mode(): void
    {
        $domain = Domain::factory()->create();

        $job = new CheckDomainJob($domain, synchronous: true);

        $this->assertTrue($job->synchronous);
    }
}
