<?php

namespace Tests\Unit;

use App\Models\Domain;
use App\Support\MonitoredUrlHelper;
use PHPUnit\Framework\TestCase;

class MonitoredUrlHelperTest extends TestCase
{
    public function test_accepts_same_host_url(): void
    {
        $domain = new Domain(['url' => 'https://www.example.com']);

        $normalized = MonitoredUrlHelper::normalizeForDomain(
            'https://example.com/page',
            $domain
        );

        $this->assertSame('https://example.com/page', $normalized);
    }

    public function test_rejects_foreign_host(): void
    {
        $domain = new Domain(['url' => 'https://example.com']);

        $this->assertNull(
            MonitoredUrlHelper::normalizeForDomain('https://evil.example/page', $domain)
        );
    }

    public function test_rejects_private_ip_literal(): void
    {
        $domain = new Domain(['url' => 'https://example.com']);

        $this->assertNull(
            MonitoredUrlHelper::normalizeForDomain('https://192.168.0.1/', $domain)
        );
    }

    public function test_filter_sitemaps_keeps_same_host_only(): void
    {
        $domain = new Domain(['url' => 'https://example.com']);

        $filtered = MonitoredUrlHelper::filterSitemapsForDomain([
            'https://example.com/sitemap.xml',
            'https://evil.example/sitemap.xml',
        ], $domain);

        $this->assertSame(['https://example.com/sitemap.xml'], $filtered);
    }
}
