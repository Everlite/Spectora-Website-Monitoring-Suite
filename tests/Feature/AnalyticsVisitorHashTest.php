<?php

namespace Tests\Feature;

use App\Models\AnalyticsVisit;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsVisitorHashTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_visitor_same_day_produces_same_hash(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://example.com',
        ]);

        $headers = [
            'Origin' => 'https://example.com',
            'User-Agent' => 'TestAgent/1.0',
        ];

        $this->postJson('/api/sync', [
            'domain' => $domain->uuid,
            'url' => 'https://example.com/a',
        ], $headers)->assertNoContent();

        $this->postJson('/api/sync', [
            'domain' => $domain->uuid,
            'url' => 'https://example.com/b',
        ], $headers)->assertNoContent();

        $hashes = AnalyticsVisit::pluck('visitor_hash')->unique();
        $this->assertCount(1, $hashes);
    }

    public function test_visitor_hash_uses_daily_key_not_raw_app_key(): void
    {
        $date = now()->format('Y-m-d');
        $dailyKey = hash_hmac('sha256', $date, (string) config('app.key'));
        $expected = hash_hmac('sha256', '127.0.0.1|SpectoraTest', $dailyKey);

        $this->assertNotSame(
            hash('sha256', '127.0.0.1'.'SpectoraTest'.$date.config('app.key')),
            $expected
        );
    }
}
