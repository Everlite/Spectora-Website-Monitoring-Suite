<?php

namespace Tests\Unit;

use App\Models\Domain;
use App\SpectoraEngine\Watchdog\SpectoraWatchdogEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpectoraWatchdogEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_watchdog_detects_obfuscated_eval_payload(): void
    {
        $domain = Domain::factory()->create(['url' => 'https://1.1.1.1']);
        $html = '<html><head><script>eval(String.fromCharCode(97,108,101,114,116))</script></head><body>Clean</body></html>';

        $engine = new SpectoraWatchdogEngine;
        $result = $engine->scan($domain, prefetchedBody: $html, prefetchedStatus: 200);

        $this->assertTrue($result->isDangerous());
        $this->assertEquals('danger', $result->status);
        $this->assertGreaterThan(0, $result->summary['critical']);
    }

    public function test_watchdog_detects_japanese_seo_spam(): void
    {
        $domain = Domain::factory()->create(['url' => 'https://1.1.1.1']);
        $html = '<html><head><title>Cheap Watches 激安 通販</title></head><body>Normal content</body></html>';

        $engine = new SpectoraWatchdogEngine;
        $result = $engine->scan($domain, prefetchedBody: $html, prefetchedStatus: 200);

        $this->assertTrue($result->isDangerous());
    }

    public function test_watchdog_passes_for_clean_html(): void
    {
        $domain = Domain::factory()->create(['url' => 'https://1.1.1.1']);
        $html = '<html><head><title>My Clean Business Website</title></head><body><h1>Welcome</h1><p>Legitimate services.</p></body></html>';

        $engine = new SpectoraWatchdogEngine;
        $result = $engine->scan($domain, prefetchedBody: $html, prefetchedStatus: 200);

        $this->assertEquals('safe', $result->status);
        $this->assertFalse($result->isDangerous());
    }
}
