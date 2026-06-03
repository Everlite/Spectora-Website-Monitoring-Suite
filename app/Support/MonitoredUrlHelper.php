<?php

namespace App\Support;

use App\Models\Domain;
use App\Services\SecurityService;

class MonitoredUrlHelper
{
    /**
     * Normalize and validate a monitored URL for the given domain (same host, SSRF-safe).
     */
    public static function normalizeForDomain(string $url, Domain $domain): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $url = 'https://'.$url;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        if (! HostHelper::matches(HostHelper::fromUrl($url), HostHelper::fromUrl($domain->url))) {
            return null;
        }

        if (! SecurityService::isSafeUrl($url)) {
            return null;
        }

        return rtrim($url, '/');
    }

    /**
     * @param  list<mixed>  $sitemaps
     * @return list<string>
     */
    public static function filterSitemapsForDomain(array $sitemaps, Domain $domain): array
    {
        $allowed = [];

        foreach ($sitemaps as $sitemap) {
            if (! is_string($sitemap)) {
                continue;
            }

            $normalized = self::normalizeForDomain($sitemap, $domain);

            if ($normalized !== null) {
                $allowed[] = $normalized;
            }
        }

        return array_values(array_unique($allowed));
    }
}
