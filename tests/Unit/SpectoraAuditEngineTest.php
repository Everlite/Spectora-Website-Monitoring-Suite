<?php

namespace Tests\Unit;

use App\Models\Domain;
use App\SpectoraEngine\Audit\SpectoraAuditEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SpectoraAuditEngineTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_engine_calculates_high_score_and_grade_a_for_optimized_site(): void
    {
        $domain = Domain::factory()->create([
            'url' => 'https://1.1.1.1',
        ]);

        $html = '<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width, initial-scale=1">'
            .'<title>Optimized Title for Search Snippets</title>'
            .'<meta name="description" content="A rich and descriptive meta description that passes the audit standards.">'
            .'</head><body>'
            .'<h1>Welcome to Fast Website</h1>'
            .'<img src="/logo.png" alt="Company Logo">'
            .'<p>Content</p>'
            .'</body></html>';

        Http::fake([
            'https://1.1.1.1' => Http::response($html, 200, [
                'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
                'X-Frame-Options' => 'SAMEORIGIN',
                'X-Content-Type-Options' => 'nosniff',
            ]),
        ]);

        $engine = new SpectoraAuditEngine;
        $result = $engine->audit($domain);

        $this->assertGreaterThanOrEqual(90, $result->score);
        $this->assertContains($result->grade, ['A+', 'A']);
        $this->assertNotEmpty($result->findings);
    }

    public function test_audit_engine_penalizes_missing_title_and_h1(): void
    {
        $domain = Domain::factory()->create([
            'url' => 'http://1.1.1.1', // HTTP penalty
        ]);

        $html = '<!DOCTYPE html><html><head></head><body><img src="/a.png"><p>No title, no h1</p></body></html>';

        Http::fake([
            'http://1.1.1.1' => Http::response($html, 200),
        ]);

        $engine = new SpectoraAuditEngine;
        $result = $engine->audit($domain);

        $this->assertLessThan(80, $result->score);
    }
}
