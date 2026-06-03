<?php

namespace Tests\Feature;

use App\Mail\DomainWarningMail;
use App\Models\Domain;
use App\Models\User;
use App\Services\DomainAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DomainAlertServiceMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_downtime_alerts_mails_owner_and_admin(): void
    {
        Mail::fake();

        $owner = User::factory()->create(['email' => 'owner@example.test']);
        User::factory()->admin()->create(['email' => 'admin@example.test']);
        $domain = Domain::factory()->create(['user_id' => $owner->id]);

        DomainAlertService::sendDowntimeAlerts($domain, ['❌ Unreachable (HTTP 500)']);

        Mail::assertSent(DomainWarningMail::class, 2);
    }
}
