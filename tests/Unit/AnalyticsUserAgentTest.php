<?php

namespace Tests\Unit;

use App\Support\AnalyticsUserAgent;
use PHPUnit\Framework\TestCase;

class AnalyticsUserAgentTest extends TestCase
{
    public function test_edge_is_not_detected_as_chrome(): void
    {
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0';

        $this->assertSame('Edge', AnalyticsUserAgent::browser($ua));
    }

    public function test_opera_is_not_detected_as_chrome(): void
    {
        $ua = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 OPR/106.0.0.0';

        $this->assertSame('Opera', AnalyticsUserAgent::browser($ua));
    }

    public function test_chrome_still_detected(): void
    {
        $ua = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

        $this->assertSame('Chrome', AnalyticsUserAgent::browser($ua));
    }
}
