<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SitemapService
{
    /**
     * Finds sitemaps for a domain by checking robots.txt and common locations.
     */
    public function discover(string $url): array
    {
        $parsedUrl = parse_url($url);
        $baseUrl = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? '');
        $sitemaps = [];

        // 1. Check robots.txt for "Sitemap:" entries
        try {
            $robotsUrl = $baseUrl . '/robots.txt';
            $response = Http::timeout(5)->get($robotsUrl);
            if ($response->successful()) {
                preg_match_all('/^Sitemap:\s*(.*)$/im', $response->body(), $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $match) {
                        $sitemaps[] = trim($match);
                    }
                }
            }
        } catch (\Exception $e) { /* ignore */ }

        // 2. Common locations
        $commonLocations = [
            '/sitemap.xml',
            '/sitemap_index.xml',
            '/sitemap-index.xml',
            '/wp-sitemap.xml',
        ];

        foreach ($commonLocations as $location) {
            $testUrl = $baseUrl . $location;
            if (in_array($testUrl, $sitemaps)) continue;

            try {
                $response = Http::timeout(3)->head($testUrl);
                if ($response->successful()) {
                    $sitemaps[] = $testUrl;
                }
            } catch (\Exception $e) { /* ignore */ }
        }

        return array_unique($sitemaps);
    }

    /**
     * Parses a sitemap (or sitemap index) and returns a list of URLs/sub-sitemaps.
     */
    public function parse(string $sitemapUrl): array
    {
        try {
            $response = Http::timeout(10)->get($sitemapUrl);
            if (!$response->successful()) return [];

            $xml = simplexml_load_string($response->body());
            if (!$xml) return [];

            $result = [
                'type' => $xml->getName() === 'sitemapindex' ? 'index' : 'urlset',
                'items' => [],
            ];

            if ($result['type'] === 'index') {
                foreach ($xml->sitemap as $sitemap) {
                    $result['items'][] = (string) $sitemap->loc;
                }
            } else {
                foreach ($xml->url as $url) {
                    $result['items'][] = (string) $url->loc;
                }
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Sitemap parsing failed for {$sitemapUrl}: " . $e->getMessage());
            return [];
        }
    }
}
