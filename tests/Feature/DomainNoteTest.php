<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_note_with_content_and_user_id(): void
    {
        $user = User::factory()->create(['first_name' => 'Anna', 'last_name' => 'Admin']);
        $domain = Domain::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->postJson("/domains/{$domain->uuid}/notes", [
                'content' => 'Client prefers Friday deploys.',
            ]);

        $response->assertOk()
            ->assertJsonPath('content', 'Client prefers Friday deploys.')
            ->assertJsonPath('user_id', $user->id)
            ->assertJsonPath('author_name', 'Anna Admin');

        $this->assertDatabaseHas('domain_notes', [
            'domain_id' => $domain->id,
            'user_id' => $user->id,
            'content' => 'Client prefers Friday deploys.',
        ]);
    }
}
