<?php

namespace Tests\Unit;

use App\Services\GeoResolutionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GeoResolutionServiceTest extends TestCase
{
    public function test_off_precision_returns_empty_geo(): void
    {
        Config::set('analytics.trusted_proxies', '*');

        $service = new GeoResolutionService;
        $request = Request::create('/api/sync', 'POST', [], [], [], [
            'HTTP_CF_IPCOUNTRY' => 'DE',
            'HTTP_CF_IPCITY' => 'Hamburg',
        ]);

        $geo = $service->resolve('203.0.113.1', $request, 'off');

        $this->assertNull($geo['country']);
        $this->assertNull($geo['city']);
    }

    public function test_country_precision_strips_city(): void
    {
        Config::set('analytics.trusted_proxies', '*');

        $service = new GeoResolutionService;
        $request = Request::create('/api/sync', 'POST', [], [], [], [
            'HTTP_CF_IPCOUNTRY' => 'DE',
            'HTTP_CF_IPREGION' => 'Hamburg',
            'HTTP_CF_IPCITY' => 'Hamburg',
        ]);

        $geo = $service->resolve('203.0.113.1', $request, 'country');

        $this->assertSame('DE', $geo['country']);
        $this->assertNull($geo['city']);
        $this->assertNull($geo['region']);
    }

    public function test_city_precision_uses_cloudflare_headers_when_trusted(): void
    {
        Config::set('analytics.trusted_proxies', '*');

        $service = new GeoResolutionService;
        $request = Request::create('/api/sync', 'POST', [], [], [], [
            'HTTP_CF_IPCOUNTRY' => 'DE',
            'HTTP_CF_IPREGION' => 'Hamburg',
            'HTTP_CF_IPCITY' => 'Hamburg',
        ]);

        $geo = $service->resolve('203.0.113.1', $request, 'city');

        $this->assertSame('DE', $geo['country']);
        $this->assertSame('Hamburg', $geo['region']);
        $this->assertSame('Hamburg', $geo['city']);
    }

    public function test_ignores_proxy_headers_when_not_trusted(): void
    {
        Config::set('analytics.trusted_proxies', null);

        $service = new GeoResolutionService;
        $request = Request::create('/api/sync', 'POST', [], [], [], [
            'HTTP_CF_IPCOUNTRY' => 'DE',
            'HTTP_CF_IPCITY' => 'Hamburg',
        ]);

        $geo = $service->resolve('203.0.113.1', $request, 'city');

        $this->assertNull($geo['country']);
        $this->assertNull($geo['city']);
    }
}
