<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Http\Request;

class GeoResolutionService
{
    private static ?Reader $reader = null;

    /**
     * @return array{country: ?string, region: ?string, city: ?string}
     */
    public function resolve(?string $ip, Request $request, string $precision): array
    {
        $empty = ['country' => null, 'region' => null, 'city' => null];

        if ($precision === 'off' || $ip === null || $ip === '') {
            return $empty;
        }

        $geo = $this->lookup($ip, $request);

        if ($precision === 'country') {
            return [
                'country' => $geo['country'],
                'region' => null,
                'city' => null,
            ];
        }

        return $geo;
    }

    /**
     * @return array{country: ?string, region: ?string, city: ?string}
     */
    private function lookup(string $ip, Request $request): array
    {
        if ($this->trustsProxyHeaders()) {
            $fromProxy = $this->fromProxyHeaders($request);
            if ($fromProxy['country'] !== null) {
                return $fromProxy;
            }
        }

        return $this->fromGeoLite2($ip);
    }

    private function trustsProxyHeaders(): bool
    {
        $proxies = config('analytics.trusted_proxies');

        return $proxies !== null && $proxies !== '';
    }

    /**
     * @return array{country: ?string, region: ?string, city: ?string}
     */
    private function fromProxyHeaders(Request $request): array
    {
        $country = $this->normalizeCountryCode($request->header('CF-IPCountry'));
        $region = $this->normalizeLabel($request->header('CF-IPRegion'));
        $city = $this->normalizeLabel($request->header('CF-IPCity'));

        return [
            'country' => $country,
            'region' => $region,
            'city' => $city,
        ];
    }

    /**
     * @return array{country: ?string, region: ?string, city: ?string}
     */
    private function fromGeoLite2(string $ip): array
    {
        $reader = $this->reader();
        if ($reader === null) {
            return ['country' => null, 'region' => null, 'city' => null];
        }

        if (! SecurityService::isSafeIp($ip)) {
            return ['country' => null, 'region' => null, 'city' => null];
        }

        try {
            $record = $reader->city($ip);
        } catch (AddressNotFoundException) {
            return ['country' => null, 'region' => null, 'city' => null];
        } catch (\Throwable) {
            return ['country' => null, 'region' => null, 'city' => null];
        }

        $country = $this->normalizeCountryCode($record->country->isoCode ?? null);
        $region = $this->normalizeLabel($record->mostSpecificSubdivision->name ?? null);
        $city = $this->normalizeLabel($record->city->name ?? null);

        return [
            'country' => $country,
            'region' => $region,
            'city' => $city,
        ];
    }

    private function reader(): ?Reader
    {
        if (self::$reader !== null) {
            return self::$reader;
        }

        $path = config('analytics.geolite2_path');
        if (! is_string($path) || $path === '' || ! is_readable($path)) {
            return null;
        }

        try {
            self::$reader = new Reader($path);

            return self::$reader;
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeCountryCode(?string $code): ?string
    {
        if ($code === null || $code === '' || strtoupper($code) === 'XX') {
            return null;
        }

        $code = strtoupper(trim($code));

        return strlen($code) === 2 ? $code : null;
    }

    private function normalizeLabel(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '' || strcasecmp($value, 'unknown') === 0) {
            return null;
        }

        return mb_substr($value, 0, 128);
    }
}
