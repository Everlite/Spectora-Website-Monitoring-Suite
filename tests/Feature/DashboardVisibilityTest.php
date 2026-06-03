<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_all_team_domains_on_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();

        $memberDomain = Domain::factory()->create([
            'user_id' => $member->id,
            'url' => 'https://member-client.example',
        ]);

        $adminDomain = Domain::factory()->create([
            'user_id' => $admin->id,
            'url' => 'https://admin-client.example',
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee($memberDomain->url, false)
            ->assertSee($adminDomain->url, false);
    }

    public function test_non_admin_sees_only_own_domains_on_dashboard(): void
    {
        $member = User::factory()->create();
        $other = User::factory()->create();

        $own = Domain::factory()->create([
            'user_id' => $member->id,
            'url' => 'https://my-client.example',
        ]);

        Domain::factory()->create([
            'user_id' => $other->id,
            'url' => 'https://other-client.example',
        ]);

        $this->actingAs($member)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee($own->url, false)
            ->assertDontSee('other-client.example', false);
    }
}
