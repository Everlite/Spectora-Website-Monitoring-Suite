<?php

namespace Tests\Unit;

use App\Models\Domain;
use App\SpectoraEngine\SpectoraEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SpectoraEngineProbeTest extends TestCase
{
    use RefreshDatabase;

    public function test_probe_writes_history_and_reuses_one_http_fetch_for_watchdog(): void
    {
        $domain = Domain::factory()->create([
            'url' => 'https://1.1.1.1',
            'notify_sent' => false,
        ]);

        $html = '<html><head><title>Shop</title><script>eval(String.fromCharCode(97,108,101,114,116))</script></head><body>ok</body></html>';

        Http::fake([
            'https://1.1.1.1' => Http::response($html, 200),
        ]);

        $result = app(SpectoraEngine::class)->probe($domain);

        $this->assertTrue($result->ran);
        $this->assertSame(200, $result->statusCode);
        $this->assertSame('danger', $result->safetyStatus);
        $this->assertArrayHasKey('watchdog', $result->safetyDetails);

        Http::assertSentCount(1);

        $this->assertDatabaseHas('checks_history', [
            'domain_id' => $domain->id,
            'status_code' => 200,
            'safety_status' => 'danger',
        ]);

        $this->assertSame('danger', $domain->fresh()->safety_status);
    }
}
