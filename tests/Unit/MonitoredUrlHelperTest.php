<?php

namespace Tests\Unit;

use App\Models\Domain;
use App\Support\MonitoredUrlHelper;
use PHPUnit\Framework\TestCase;

class MonitoredUrlHelperTest extends TestCase
{
    public function test_accepts_same_host_url(): void
    {
        $domain = new Domain(['url' => 'https://www.client.example']);

        $normalized = MonitoredUrlHelper::normalizeForDomain(
            'https://client.example/page',
            $domain
        );

        $this->assertSame('https://client.example/page', $normalized);
    }

    public function test_rejects_foreign_host(): void
    {
        $domain = new Domain(['url' => 'https://client.example']);

        $this->assertNull(
            MonitoredUrlHelper::normalizeForDomain('https://evil.example/page', $domain)
        );
    }

    public function test_rejects_private_ip_literal(): void
    {
        $domain = new Domain(['url' => 'https://client.example']);

        $this->assertNull(
            MonitoredUrlHelper::normalizeForDomain('https://192.168.0.1/', $domain)
        );
    }
}
