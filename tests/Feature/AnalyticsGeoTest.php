<?php

namespace Tests\Feature;

use App\Models\AnalyticsVisit;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsGeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['analytics.trusted_proxies' => '*']);
    }

    public function test_sync_stores_city_geo_when_precision_is_city(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'analytics_geo_precision' => 'city',
        ]);

        $this->postJson('/api/sync', [
            'domain' => $domain->uuid,
            'url' => 'https://example.com/shop',
            'width' => 390,
        ], [
            'Origin' => 'https://example.com',
            'CF-IPCountry' => 'DE',
            'CF-IPRegion' => 'Hamburg',
            'CF-IPCity' => 'Hamburg',
        ])->assertNoContent();

        $visit = AnalyticsVisit::first();
        $this->assertSame('DE', $visit->country);
        $this->assertSame('Hamburg', $visit->city);
        $this->assertSame('mobile', $visit->device);
    }

    public function test_sync_skips_geo_when_precision_off(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'analytics_geo_precision' => 'off',
        ]);

        $this->postJson('/api/sync', [
            'domain' => $domain->uuid,
            'url' => 'https://example.com/page',
        ], [
            'Origin' => 'https://example.com',
            'CF-IPCountry' => 'DE',
            'CF-IPCity' => 'Hamburg',
        ])->assertNoContent();

        $visit = AnalyticsVisit::first();
        $this->assertNull($visit->country);
        $this->assertNull($visit->city);
    }

    public function test_analytics_settings_update_geo_precision(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create([
            'user_id' => $user->id,
            'analytics_geo_precision' => 'city',
        ]);

        $this->actingAs($user)
            ->postJson(route('domains.analytics.settings', $domain), [
                'analytics_geo_precision' => 'country',
            ])
            ->assertOk();

        $this->assertSame('country', $domain->fresh()->analytics_geo_precision);
    }
}
