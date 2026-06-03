<?php

namespace Tests\Feature;

use App\Jobs\PerformSpectoraAudit;
use App\Models\Domain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PerformSpectoraAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_persists_spectora_score_and_history(): void
    {
        $domain = Domain::factory()->create([
            'url' => 'https://1.1.1.1',
            'pagespeed_score_desktop' => null,
        ]);

        $html = '<!DOCTYPE html><html><head><title>OK</title></head><body>'
            .'<img src="/a.png" alt="logo">'
            .'<p>Healthy page content</p></body></html>';

        Http::fake([
            'https://1.1.1.1' => Http::response($html, 200),
        ]);

        (new PerformSpectoraAudit($domain))->handle();

        $domain->refresh();

        $this->assertNotNull($domain->pagespeed_score_desktop);
        $this->assertIsArray($domain->last_pagespeed_details);
        $this->assertDatabaseHas('checks_history', [
            'domain_id' => $domain->id,
            'pagespeed_score_desktop' => $domain->pagespeed_score_desktop,
        ]);
    }
}
