<?php

namespace Tests\Unit;

use App\Models\Domain;
use App\SpectoraEngine\Pulse\PulseAnonymizer;
use App\SpectoraEngine\Pulse\PulseEvent;
use App\SpectoraEngine\Pulse\PulseIngestEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PulseIngestEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_pulse_anonymizer_generates_consistent_daily_hash(): void
    {
        $hash1 = PulseAnonymizer::generateVisitorHash('192.168.1.50', 'Mozilla/5.0', '2026-06-15');
        $hash2 = PulseAnonymizer::generateVisitorHash('192.168.1.50', 'Mozilla/5.0', '2026-06-15');
        $hashNextDay = PulseAnonymizer::generateVisitorHash('192.168.1.50', 'Mozilla/5.0', '2026-06-16');

        $this->assertEquals($hash1, $hash2);
        $this->assertNotEquals($hash1, $hashNextDay);
    }

    public function test_pulse_ingest_stores_pageview_and_custom_event(): void
    {
        $domain = Domain::factory()->create(['url' => 'https://example.com']);
        $engine = app(PulseIngestEngine::class);

        $request = Request::create('/api/sync', 'POST', [], [], [], [
            'HTTP_ORIGIN' => 'https://example.com',
            'REMOTE_ADDR' => '1.1.1.1',
        ]);

        $event = new PulseEvent(
            domainUuid: $domain->uuid,
            url: 'https://example.com/pricing',
            eventType: 'custom',
            eventName: 'lead_form_submitted'
        );

        $visit = $engine->ingest($request, $event);

        $this->assertNotNull($visit->id);
        $this->assertEquals($domain->id, $visit->domain_id);
        $this->assertEquals('/pricing', $visit->path);
    }
}
