<?php

namespace Tests\Unit;

use App\Models\Domain;
use App\Services\MonitoringFilterService;
use App\Services\RobotsTxtService;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Client\Response as HttpClientResponse;
use PHPUnit\Framework\TestCase;

class MonitoringFilterNoindexTest extends TestCase
{
    private function filterService(): MonitoringFilterService
    {
        return new MonitoringFilterService(new RobotsTxtService);
    }

    private function fakeResponse(int $status, string $body, array $headers = []): HttpClientResponse
    {
        $psr = new Response($status, $headers, $body);

        return new HttpClientResponse($psr);
    }

    public function test_detects_noindex_meta_with_reversed_attribute_order(): void
    {
        $domain = new Domain(['respect_noindex' => true]);
        $html = '<html><head><meta content="noindex, nofollow" name="robots"></head><body>OK</body></html>';

        $result = $this->filterService()->shouldIgnoreResponse($domain, $this->fakeResponse(200, $html));

        $this->assertTrue($result['ignore']);
    }

    public function test_ignores_indexable_robots_meta(): void
    {
        $domain = new Domain(['respect_noindex' => true]);
        $html = '<html><head><meta name="robots" content="index, follow"></head></html>';

        $result = $this->filterService()->shouldIgnoreResponse($domain, $this->fakeResponse(200, $html));

        $this->assertFalse($result['ignore']);
    }
}
