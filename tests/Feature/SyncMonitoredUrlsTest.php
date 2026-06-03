<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncMonitoredUrlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_urls_on_foreign_host(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
        ]);

        $this->actingAs($user)
            ->postJson("/domains/{$domain->uuid}/urls/monitored", [
                'urls' => [
                    ['url' => 'https://other.example/page', 'is_monitored' => true],
                ],
            ])
            ->assertStatus(422)
            ->assertJsonPath('rejected.0', 'https://other.example/page');

        $this->assertDatabaseCount('monitored_urls', 0);
    }

    public function test_accepts_urls_on_same_host(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
        ]);

        $this->actingAs($user)
            ->postJson("/domains/{$domain->uuid}/urls/monitored", [
                'urls' => [
                    ['url' => 'https://example.com/about', 'is_monitored' => true],
                ],
            ])
            ->assertOk();

        $this->assertDatabaseHas('monitored_urls', [
            'domain_id' => $domain->id,
            'url' => 'https://example.com/about',
            'is_active' => true,
        ]);
    }
}
