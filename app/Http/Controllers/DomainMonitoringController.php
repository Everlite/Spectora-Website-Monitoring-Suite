<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Services\MonitoringFilterService;
use App\Services\SecurityService;
use App\Services\SitemapService;
use App\Support\MonitoredUrlHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class DomainMonitoringController extends Controller
{
    public function updateSettings(Request $request, Domain $domain)
    {
        $this->authorize('update', $domain);

        $validated = $request->validate([
            'only_check_public_pages' => 'boolean',
            'respect_robots_txt' => 'boolean',
            'respect_noindex' => 'boolean',
            'exclude_patterns' => 'nullable|string',
            'included_sitemaps' => 'nullable|array',
            'included_sitemaps.*' => 'string|max:2048',
        ]);

        if (isset($validated['included_sitemaps'])) {
            $validated['included_sitemaps'] = MonitoredUrlHelper::filterSitemapsForDomain(
                $validated['included_sitemaps'],
                $domain
            );
        }

        $domain->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Settings updated successfully']);
        }

        return back()->with('status', 'Settings saved.');
    }

    public function detectSitemaps(Domain $domain, SitemapService $sitemapService)
    {
        $this->authorize('update', $domain);

        $sitemaps = $sitemapService->discover($domain->url);

        $domain->update([
            'sitemap_urls' => $sitemaps,
        ]);

        return response()->json([
            'message' => count($sitemaps).' sitemaps found.',
            'sitemaps' => $sitemaps,
        ]);
    }

    public function scanUrls(Domain $domain, SitemapService $sitemapService, MonitoringFilterService $filterService)
    {
        $this->authorize('update', $domain);

        $allUrls = [];
        $domainHost = preg_replace('/^www\./', '', parse_url($domain->url, PHP_URL_HOST));

        if (! SecurityService::isSafeUrl($domain->url)) {
            Log::warning("Blocked unsafe homepage scan: {$domain->url}");

            return response()->json(['message' => 'URL blocked for security reasons.'], 422);
        }

        try {
            $response = SecurityService::http()
                ->timeout(10)
                ->withUserAgent('SpectoraBot/1.0')
                ->get($domain->url);
            if ($response->successful()) {
                $crawler = new Crawler($response->body());
                $crawler->filter('a[href]')->each(function (Crawler $node) use (&$allUrls, $domainHost, $domain) {
                    $href = $node->attr('href');
                    if (! $href || str_starts_with($href, '#') || str_starts_with($href, 'javascript:')) {
                        return;
                    }

                    if (str_starts_with($href, '//')) {
                        $href = (parse_url($domain->url, PHP_URL_SCHEME) ?: 'https').':'.$href;
                    } elseif (str_starts_with($href, '/')) {
                        $href = rtrim($domain->url, '/').$href;
                    }

                    $urlHost = preg_replace('/^www\./', '', parse_url($href, PHP_URL_HOST));
                    if ($urlHost === $domainHost) {
                        $allUrls[] = rtrim($href, '/');
                    }
                });
            }
        } catch (\Exception $e) {
            Log::warning("Homepage scan failed for {$domain->url}: ".$e->getMessage());
        }

        $sitemapsToScan = $domain->included_sitemaps;

        if (empty($sitemapsToScan)) {
            $sitemapsToScan = $sitemapService->discover($domain->url);
        }

        foreach ($sitemapsToScan as $sitemapUrl) {
            try {
                $parsed = $sitemapService->parse($sitemapUrl);
                if (! empty($parsed['items'])) {
                    foreach ($parsed['items'] as $item) {
                        $allUrls[] = rtrim($item, '/');
                    }
                }
            } catch (\Exception $e) {
            }
        }

        $uniqueUrls = array_unique($allUrls);
        $results = [];

        foreach ($uniqueUrls as $url) {
            $existing = $domain->monitoredUrls()
                ->where(function ($q) use ($url) {
                    $q->where('url', $url)->orWhere('url', $url.'/');
                })
                ->first();

            $filter = $filterService->shouldCheck($domain, $url);

            $results[] = [
                'url' => $url,
                'is_monitored' => $existing ? $existing->is_active : false,
                'is_public' => $filter['should_check'],
                'skip_reason' => $filter['should_check'] ? null : $filter['reason'],
            ];
        }

        return response()->json([
            'urls' => array_values($results),
        ]);
    }

    public function syncMonitoredUrls(Request $request, Domain $domain)
    {
        $this->authorize('update', $domain);

        $validated = $request->validate([
            'urls' => 'required|array',
            'urls.*.url' => 'required|string|max:2048',
            'urls.*.is_monitored' => 'required|boolean',
        ]);

        $rejected = [];
        $accepted = [];

        foreach ($validated['urls'] as $urlData) {
            $normalized = MonitoredUrlHelper::normalizeForDomain($urlData['url'], $domain);

            if ($normalized === null) {
                $rejected[] = $urlData['url'];

                continue;
            }

            $accepted[] = [
                'url' => $normalized,
                'is_monitored' => $urlData['is_monitored'],
            ];
        }

        if ($rejected !== []) {
            return response()->json([
                'message' => 'Some URLs were rejected (must belong to this domain and use a safe public host).',
                'rejected' => $rejected,
            ], 422);
        }

        foreach ($accepted as $urlData) {
            $domain->monitoredUrls()->updateOrCreate(
                ['url' => $urlData['url']],
                ['is_active' => $urlData['is_monitored']]
            );
        }

        return response()->json(['message' => 'URLs synchronized.']);
    }
}
