<?php

namespace Tests\Feature;

use App\Jobs\CheckDomainJob;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DomainAnalyzeTest extends TestCase
{
    use RefreshDatabase;

    public function test_analyze_runs_synchronous_domain_check_without_type_error(): void
    {
        Http::fake([
            '*' => Http::response('<html><body>ok</body></html>', 200),
        ]);

        $user = User::factory()->create();
        $domain = Domain::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
        ]);

        $this->actingAs($user)
            ->post(route('domains.analyze', $domain))
            ->assertRedirect()
            ->assertSessionHas('status');

        $domain->refresh();
        $this->assertNotNull($domain->last_checked);
    }

    public function test_dispatch_sync_accepts_domain_and_synchronous_flag(): void
    {
        Http::fake([
            '*' => Http::response('<html></html>', 200),
        ]);

        $domain = Domain::factory()->create(['url' => 'https://example.com']);

        CheckDomainJob::dispatchSync($domain, synchronous: true);

        $this->assertNotNull($domain->fresh()->last_checked);
    }
}
