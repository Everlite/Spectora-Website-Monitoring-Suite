<?php

namespace Tests\Unit;

use App\Models\Domain;
use App\Services\WatchdogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WatchdogServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_scan_detects_spam_keywords_in_prefetched_body(): void
    {
        $domain = Domain::factory()->make(['url' => 'https://example.com']);

        $service = new WatchdogService;
        $result = $service->scan(
            $domain,
            'https://example.com',
            '<html><body>Buy viagra online cheap</body></html>',
            200
        );

        $this->assertSame('danger', $result['status']);
        $this->assertGreaterThan(0, $result['summary']['critical']);
    }
}
