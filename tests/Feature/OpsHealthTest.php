<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OpsHealthTest extends TestCase
{
    public function test_ops_health_returns_ok_when_heartbeat_is_fresh(): void
    {
        Cache::put('spectora:ops:heartbeat', now(), 3600);

        $this->get('/health/ops')
            ->assertOk()
            ->assertSee('ok');
    }

    public function test_ops_health_returns_503_when_heartbeat_missing(): void
    {
        Cache::forget('spectora:ops:heartbeat');

        $this->get('/health/ops')
            ->assertStatus(503);
    }

    public function test_ops_health_not_exposed_to_remote_ips(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.50'])
            ->get('/health/ops')
            ->assertNotFound();
    }
}
