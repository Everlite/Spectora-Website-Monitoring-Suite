<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_accepts_www_origin_for_apex_domain(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
        ]);

        $response = $this->postJson('/api/sync', [
            'domain' => $domain->uuid,
            'url' => 'https://example.com/page',
        ], [
            'Origin' => 'https://www.example.com',
        ]);

        $response->assertNoContent();
        $this->assertDatabaseCount('analytics_visits', 1);
    }

    public function test_sync_rejects_wrong_origin(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
        ]);

        $response = $this->postJson('/api/sync', [
            'domain' => $domain->uuid,
            'url' => 'https://example.com/page',
        ], [
            'Origin' => 'https://evil.com',
        ]);

        $response->assertForbidden();
    }
}
