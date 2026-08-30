<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\DomainNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_sees_domain_cockpit_without_tab_bar(): void
    {
        $owner = User::factory()->create();
        $domain = Domain::factory()->create([
            'user_id' => $owner->id,
            'url' => 'https://1.1.1.1',
        ]);

        $this->actingAs($owner)
            ->get("/domains/{$domain->uuid}")
            ->assertOk()
            ->assertSee('Uptime (30d)', false)
            ->assertSee('Engine-Bericht', false)
            ->assertSee('Spectora Pulse', false)
            ->assertDontSee('Historie & Probes', false);
    }

    public function test_user_cannot_view_another_users_domain_dashboard(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($intruder)
            ->get("/domains/{$domain->uuid}")
            ->assertForbidden();
    }

    public function test_user_cannot_store_note_on_another_users_domain(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($intruder)
            ->postJson("/domains/{$domain->uuid}/notes", ['content' => 'Hacked note'])
            ->assertForbidden();

        $this->assertDatabaseCount('domain_notes', 0);
    }

    public function test_user_cannot_update_note_on_another_users_domain(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $owner->id]);
        $note = DomainNote::create([
            'domain_id' => $domain->id,
            'user_id' => $owner->id,
            'content' => 'Original',
        ]);

        $this->actingAs($intruder)
            ->patchJson("/notes/{$note->id}", ['content' => 'Changed'])
            ->assertForbidden();

        $this->assertDatabaseHas('domain_notes', [
            'id' => $note->id,
            'content' => 'Original',
        ]);
    }

    public function test_user_cannot_sync_monitored_urls_on_another_users_domain(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $domain = Domain::factory()->create([
            'user_id' => $owner->id,
            'url' => 'https://client.example',
        ]);

        $this->actingAs($intruder)
            ->postJson("/domains/{$domain->uuid}/urls/monitored", [
                'urls' => [
                    ['url' => 'https://client.example/page', 'is_monitored' => true],
                ],
            ])
            ->assertForbidden();
    }
}
