<?php

namespace Tests\Unit;

use App\Services\SecurityService;
use PHPUnit\Framework\TestCase;

class SecurityServiceTest extends TestCase
{
    public function test_rejects_loopback_ipv4(): void
    {
        $this->assertFalse(SecurityService::isSafeIp('127.0.0.1'));
        $this->assertFalse(SecurityService::isSafeUrl('http://127.0.0.1/'));
    }

    public function test_rejects_private_ranges(): void
    {
        $this->assertFalse(SecurityService::isSafeIp('10.0.0.1'));
        $this->assertFalse(SecurityService::isSafeIp('192.168.0.1'));
        $this->assertFalse(SecurityService::isSafeUrl('http://10.0.0.1/'));
    }

    public function test_accepts_public_ip_literal(): void
    {
        $this->assertTrue(SecurityService::isSafeIp('8.8.8.8'));
        $this->assertTrue(SecurityService::isSafeUrl('https://8.8.8.8/'));
    }

    public function test_rejects_localhost_hostname(): void
    {
        $this->assertFalse(SecurityService::isSafeUrl('http://localhost/'));
    }

    public function test_resolve_pins_for_public_ip_literal(): void
    {
        $pins = SecurityService::resolvePinsForUrl('https://1.1.1.1/');

        $this->assertContains('1.1.1.1:443:1.1.1.1', $pins);
    }

    public function test_resolve_pins_throws_for_unsafe_url(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('SSRF Protection');

        SecurityService::resolvePinsForUrl('http://127.0.0.1/');
    }

    public function test_resolve_pins_uses_single_dns_resolution_for_public_ip_literal(): void
    {
        $pinsA = SecurityService::resolvePinsForUrl('https://1.1.1.1/');
        $pinsB = SecurityService::resolvePinsForUrl('https://1.1.1.1/');

        $this->assertSame($pinsA, $pinsB);
        $this->assertContains('1.1.1.1:443:1.1.1.1', $pinsA);
    }
}
