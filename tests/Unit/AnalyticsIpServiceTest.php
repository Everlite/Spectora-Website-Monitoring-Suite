<?php

namespace Tests\Unit;

use App\Services\AnalyticsIpService;
use PHPUnit\Framework\TestCase;

class AnalyticsIpServiceTest extends TestCase
{
    public function test_ipv4_anonymization_zeros_last_octet(): void
    {
        $this->assertSame('203.0.113.0', AnalyticsIpService::anonymizeForHash('203.0.113.45'));
    }

    public function test_ipv6_anonymization_masks_suffix(): void
    {
        $anonymized = AnalyticsIpService::anonymizeForHash('2001:db8:85a3::8a2e:370:7334');

        $this->assertStringStartsWith('2001:db8:85a3', $anonymized);
        $this->assertNotSame('2001:db8:85a3::8a2e:370:7334', $anonymized);
    }
}
