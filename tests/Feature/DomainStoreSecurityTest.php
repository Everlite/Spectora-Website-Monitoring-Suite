<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DomainStoreSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_rejects_loopback_urls(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('domains.store'), [
                'url' => 'http://127.0.0.1',
            ])
            ->assertSessionHasErrors('url');

        $this->assertDatabaseCount('domains', 0);
    }

    public function test_store_accepts_public_url(): void
    {
        Http::fake([
            '*' => Http::response('<html></html>', 200),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('domains.store'), [
                'url' => 'https://example.com',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('domains', [
            'user_id' => $user->id,
            'url' => 'https://example.com',
        ]);
    }
}
