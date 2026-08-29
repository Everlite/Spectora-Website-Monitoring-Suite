<?php

namespace Tests\Unit;

use App\Mail\DomainRecoveryMail;
use App\Mail\DomainWarningMail;
use App\Models\Domain;
use App\Models\User;
use App\SpectoraEngine\Incidents\IncidentState;
use App\SpectoraEngine\Incidents\IncidentStateMachine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class IncidentStateMachineTest extends TestCase
{
    use RefreshDatabase;

    public function test_state_machine_fires_downtime_alert_on_first_failure(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'admin@agency.test', 'is_admin' => true]);
        $domain = Domain::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://client.test',
            'notify_sent' => false,
        ]);

        $stateMachine = new IncidentStateMachine;
        $state = $stateMachine->transition($domain, null, ['❌ Unreachable (HTTP 500)'], $domain->url);

        $this->assertEquals(IncidentState::DOWN, $state);
        $this->assertTrue($domain->fresh()->notify_sent);
        Mail::assertSent(DomainWarningMail::class);
    }

    public function test_state_machine_fires_recovery_alert_when_domain_recovers(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'admin@agency.test', 'is_admin' => true]);
        $domain = Domain::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://client.test',
            'notify_sent' => true, // Was down previously
        ]);

        $stateMachine = new IncidentStateMachine;
        $state = $stateMachine->transition($domain, null, [], $domain->url);

        $this->assertEquals(IncidentState::RECOVERED, $state);
        $this->assertFalse($domain->fresh()->notify_sent);
        Mail::assertSent(DomainRecoveryMail::class);
    }
}
