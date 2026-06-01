<?php

namespace Tests\Unit;

use App\Support\ReportChartSvg;
use PHPUnit\Framework\TestCase;

class ReportChartSvgTest extends TestCase
{
    public function test_line_chart_returns_svg_data_uri(): void
    {
        $uri = ReportChartSvg::lineChart([1.0, 2.0, 1.5, 3.0]);

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $uri);
        $decoded = base64_decode(substr($uri, strlen('data:image/svg+xml;base64,')));
        $this->assertStringContainsString('<polyline', $decoded);
    }

    public function test_bar_chart_returns_svg_data_uri(): void
    {
        $uri = ReportChartSvg::barChart([2, 5, 0, 3]);

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $uri);
        $decoded = base64_decode(substr($uri, strlen('data:image/svg+xml;base64,')));
        $this->assertStringContainsString('<rect', $decoded);
    }
}
