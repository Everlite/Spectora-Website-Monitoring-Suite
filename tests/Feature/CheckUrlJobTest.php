<?php

namespace Tests\Feature;

use App\Jobs\CheckUrlJob;
use App\Mail\DomainWarningMail;
use App\Models\Domain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckUrlJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_main_url_check_records_history_and_sends_alert_on_failure(): void
    {
        Mail::fake();

        $owner = User::factory()->create(['email' => 'owner@example.test']);
        User::factory()->create(['is_admin' => true, 'email' => 'admin@example.test']);

        $domain = Domain::factory()->create([
            'user_id' => $owner->id,
            'url' => 'https://1.1.1.1',
            'notify_sent' => false,
        ]);

        Http::fake([
            'https://1.1.1.1' => Http::response('error page', 503),
        ]);

        (new CheckUrlJob($domain))->handle();

        $this->assertDatabaseHas('checks_history', [
            'domain_id' => $domain->id,
            'status_code' => 503,
        ]);

        $domain->refresh();
        $this->assertTrue($domain->notify_sent);

        Mail::assertSent(DomainWarningMail::class, 2);
    }

    public function test_healthy_check_resets_notify_sent_flag(): void
    {
        Mail::fake();

        $domain = Domain::factory()->create([
            'url' => 'https://1.1.1.1',
            'notify_sent' => true,
        ]);

        Http::fake([
            'https://1.1.1.1' => Http::response('all good', 200),
        ]);

        (new CheckUrlJob($domain))->handle();

        $this->assertFalse($domain->fresh()->notify_sent);
        Mail::assertNothingSent();
    }
}
