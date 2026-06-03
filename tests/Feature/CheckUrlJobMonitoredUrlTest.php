<?php

namespace Tests\Feature;

use App\Jobs\CheckUrlJob;
use App\Mail\DomainWarningMail;
use App\Models\Domain;
use App\Models\MonitoredUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckUrlJobMonitoredUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitored_sub_url_failure_sends_alert_with_checked_url(): void
    {
        Mail::fake();

        $owner = User::factory()->create(['email' => 'owner@example.test']);

        $domain = Domain::factory()->create([
            'user_id' => $owner->id,
            'url' => 'https://1.1.1.1',
            'notify_sent' => false,
        ]);

        $monitored = MonitoredUrl::create([
            'domain_id' => $domain->id,
            'url' => 'https://8.8.8.8/page',
            'is_active' => true,
            'notify_sent' => false,
        ]);

        Http::fake([
            'https://8.8.8.8/page' => Http::response('down', 503),
        ]);

        (new CheckUrlJob($domain, monitoredUrl: $monitored))->handle();

        $monitored->refresh();
        $this->assertTrue($monitored->notify_sent);
        $this->assertFalse($domain->fresh()->notify_sent);

        Mail::assertSent(DomainWarningMail::class, function (DomainWarningMail $mail) {
            return $mail->checkedUrl === 'https://8.8.8.8/page';
        });
    }
}
