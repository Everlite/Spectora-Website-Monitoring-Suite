<?php

namespace Tests\Feature;

use App\Models\ChecksHistory;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainUptimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_uptime_ignores_audit_only_history_rows(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $user->id]);

        ChecksHistory::create([
            'domain_id' => $domain->id,
            'status_code' => 200,
            'response_time' => 0.42,
            'created_at' => now(),
        ]);

        ChecksHistory::create([
            'domain_id' => $domain->id,
            'pagespeed_score_desktop' => 88,
            'created_at' => now(),
        ]);

        $this->assertSame(100.0, $domain->calculateUptime(30));
    }

    public function test_uptime_counts_failed_checks(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $user->id]);

        ChecksHistory::create([
            'domain_id' => $domain->id,
            'status_code' => 200,
            'response_time' => 0.5,
            'created_at' => now(),
        ]);

        ChecksHistory::create([
            'domain_id' => $domain->id,
            'status_code' => 500,
            'response_time' => 1.2,
            'created_at' => now(),
        ]);

        $this->assertSame(50.0, $domain->calculateUptime(30));
    }
}
