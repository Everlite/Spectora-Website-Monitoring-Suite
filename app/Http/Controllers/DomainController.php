<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\MonitoredUrl;
use App\Services\AnalyticsQueryService;
use App\Services\SitemapService;
use App\Services\MonitoringFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class DomainController extends Controller
{
    public function __construct(
        private readonly AnalyticsQueryService $analyticsQuery
    ) {}

    public function updateSettings(Request $request, Domain $domain)
    {
        $this->authorize('update', $domain);

        $validated = $request->validate([
            'only_check_public_pages' => 'boolean',
            'respect_robots_txt' => 'boolean',
            'respect_noindex' => 'boolean',
            'exclude_patterns' => 'nullable|string',
            'included_sitemaps' => 'nullable|array',
        ]);

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
            'sitemap_urls' => $sitemaps
        ]);

        return response()->json([
            'message' => count($sitemaps) . ' sitemaps found.',
            'sitemaps' => $sitemaps
        ]);
    }

    public function scanUrls(Domain $domain, SitemapService $sitemapService, MonitoringFilterService $filterService)
    {
        $this->authorize('update', $domain);

        $allUrls = [];
        $domainHost = preg_replace('/^www\./', '', parse_url($domain->url, PHP_URL_HOST));

        // 1. Scan Homepage Links with SSRF Middleware
        if (!\App\Services\SecurityService::isSafeUrl($domain->url)) {
            \Illuminate\Support\Facades\Log::warning("Blocked unsafe homepage scan: {$domain->url}");

            return response()->json(['message' => 'URL blocked for security reasons.'], 422);
        }

        try {
            $response = Http::withMiddleware(\App\Services\SecurityService::redirectMiddleware())
                ->timeout(10)
                ->withUserAgent('SpectoraBot/1.0')
                ->get($domain->url);
            if ($response->successful()) {
                $crawler = new Crawler($response->body());
                $crawler->filter('a[href]')->each(function (Crawler $node) use (&$allUrls, $domainHost, $domain) {
                    $href = $node->attr('href');
                    if (!$href || str_starts_with($href, '#') || str_starts_with($href, 'javascript:')) return;
                    
                    // Normalize relative links
                    if (str_starts_with($href, '//')) {
                        $href = (parse_url($domain->url, PHP_URL_SCHEME) ?: 'https') . ':' . $href;
                    } elseif (str_starts_with($href, '/')) {
                        $href = rtrim($domain->url, '/') . $href;
                    }
                    
                    $urlHost = preg_replace('/^www\./', '', parse_url($href, PHP_URL_HOST));
                    if ($urlHost === $domainHost) {
                        $allUrls[] = rtrim($href, '/');
                    }
                });
            }
        } catch (\Exception $e) { 
            \Illuminate\Support\Facades\Log::warning("Homepage scan failed for {$domain->url}: " . $e->getMessage());
        }

        // 2. Scan Sitemaps
        $sitemapsToScan = $domain->included_sitemaps;
        
        // If no sitemaps selected, try auto-discovery to give user some results
        if (empty($sitemapsToScan)) {
            $sitemapsToScan = $sitemapService->discover($domain->url);
        }

        foreach ($sitemapsToScan as $sitemapUrl) {
            try {
                $parsed = $sitemapService->parse($sitemapUrl);
                if (!empty($parsed['items'])) {
                    foreach ($parsed['items'] as $item) {
                         $allUrls[] = rtrim($item, '/');
                    }
                }
            } catch (\Exception $e) { /* ignore single sitemap failure */ }
        }

        // 3. Unique & Clean
        $uniqueUrls = array_unique($allUrls);
        $results = [];

        foreach ($uniqueUrls as $url) {
            // Check if already monitored (normalize both for comparison)
            $existing = $domain->monitoredUrls()
                ->where(function ($q) use ($url) {
                    $q->where('url', $url)->orWhere('url', $url . '/');
                })
                ->first();
            
            // Check if "public" per filter
            $filter = $filterService->shouldCheck($domain, $url);
            
            $results[] = [
                'url' => $url,
                'is_monitored' => $existing ? $existing->is_active : false,
                'is_public' => $filter['should_check'],
                'skip_reason' => $filter['should_check'] ? null : $filter['reason'],
            ];
        }

        return response()->json([
            'urls' => array_values($results)
        ]);
    }

    public function syncMonitoredUrls(Request $request, Domain $domain)
    {
        $this->authorize('update', $domain);

        $validated = $request->validate([
            'urls' => 'required|array',
            'urls.*.url' => 'required|string',
            'urls.*.is_monitored' => 'required|boolean',
        ]);

        foreach ($validated['urls'] as $urlData) {
            $domain->monitoredUrls()->updateOrCreate(
                ['url' => rtrim($urlData['url'], '/')],
                ['is_active' => $urlData['is_monitored']]
            );
        }

        return response()->json(['message' => 'URLs synchronized.']);
    }

    public function history(Domain $domain)
    {
        $this->authorize('view', $domain);

        $showOnlyErrors = request()->has('only_errors');
        $dateFilter = request()->input('date');

        $query = $domain->uptimeHistory()->orderBy('created_at', 'desc');

        if ($showOnlyErrors) {
            $query->failedUptime();
        }

        if ($dateFilter) {
            $query->whereDate('created_at', $dateFilter);
        }

        $checks = $query->paginate(20);

        $chartQuery = $domain->uptimeHistory()->orderBy('created_at', 'desc');
        if ($dateFilter) {
            $chartQuery->whereDate('created_at', $dateFilter);
            $chartData = $chartQuery->get()->reverse();
        } else {
            $chartData = $chartQuery->take(50)->get()->reverse();
        }

        $labels = $chartData->map(fn($check) => $check->created_at->format('d.m. H:i'))->values();
        $dataPoints = $chartData->pluck('response_time')->values();

        return view('domains.history', compact('domain', 'checks', 'labels', 'dataPoints', 'showOnlyErrors', 'dateFilter'));
    }

    public function show(Domain $domain)
    {
        $this->authorize('view', $domain);

        $analytics = $this->analyticsQuery->getDashboardData($domain);

        // --- 2. History & KPIs ---
        // Uptime (Last 30 days based on KPI)
        $uptime = $domain->calculateUptime(30);

        // Uptime History for Sparkline (Last 7 days)
        $uptimeHistory = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayQuery = $domain->uptimeHistory()->whereDate('created_at', $date);
            $dayTotal = (clone $dayQuery)->count();
            if ($dayTotal === 0) {
                $uptimeHistory[] = 0;
            } else {
                $dayFailed = (clone $dayQuery)->failedUptime()->count();
                $uptimeHistory[] = round((($dayTotal - $dayFailed) / $dayTotal) * 100, 1);
            }
        }

        // Avg Response Time (24h)
        $avgResponseTime = $domain->uptimeHistory()
            ->where('created_at', '>=', now()->subDay())
            ->avg('response_time');
        
        // Stored as seconds in history, so convert to ms for the dashboard
        $avgResponseTime = round(($avgResponseTime ?? 0) * 1000);

        // Recent Checks (Logbook)
        $recentChecks = $domain->uptimeHistory()->orderBy('created_at', 'desc')->paginate(20);
        
        // Monitored URLs for the Overview tab
        $monitoredUrls = $domain->monitoredUrls()->where('is_active', true)->get();

        // History Chart (Response Time) - Align with Analytics Chart if possible
        $historyChartData = $domain->uptimeHistory()->orderBy('created_at', 'desc')->take(50)->get()->reverse();
        $historyLabels = $historyChartData->map(fn($h) => $h->created_at->format('d.m. H:i'))->values();
        $historyResponseTimes = $historyChartData->map(fn($h) => round($h->response_time * 1000))->values();


        // --- 3. Performance & Security ---
        // SSL
        $sslDaysRemaining = $domain->ssl_days_left ?? 0;

        // PageSpeed History for Chart
        $psHistory = $domain->history()
            ->whereNotNull('pagespeed_score_desktop')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->reverse();
        $psHistoryLabels = $psHistory->map(fn($h) => $h->created_at->format('d.m.'))->values();
        $psHistoryScores = $psHistory->pluck('pagespeed_score_desktop')->values();

        // Main Performance Score
        $score = $domain->pagespeed_score_desktop ?? 0;
        $scoreColor = $score >= 90 ? 'emerald' : ($score >= 50 ? 'amber' : 'rose');

        // Watchdog / Security
        $watchdogData = $domain->safety_details['watchdog'] ?? [];

        // --- 4. Notes ---
        $notes = $domain->notes()->orderBy('created_at', 'desc')->get();

        // --- 5. Security & Audit Summary (Restored Fix) ---
        $auditDetails = $domain->last_pagespeed_details ?? [];
        $criticalCount = collect($auditDetails)->where('status', 'error')->count();
        $warningCount = collect($auditDetails)->where('status', 'warning')->count();

        return view('domains.dashboard', array_merge(
            compact(
                'domain',
                'uptime',
                'uptimeHistory',
                'avgResponseTime',
                'sslDaysRemaining',
                'recentChecks',
                'monitoredUrls',
                'historyLabels',
                'historyResponseTimes',
                'psHistoryLabels',
                'psHistoryScores',
                'criticalCount',
                'warningCount',
                'auditDetails',
                'score',
                'scoreColor',
                'watchdogData',
                'notes'
            ),
            $analytics
        ));
    }

    public function analyze(Request $request, Domain $domain)
    {
        $this->authorize('update', $domain);

        // Dispatch Jobs Synchronously
        \App\Jobs\PerformSpectoraAudit::dispatchSync($domain);
        \App\Jobs\CheckDomainJob::dispatchSync($domain);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Analysis started']);
        }

        return back()->with('status', "Spectora audit started. Refresh in a few seconds.");
    }

    public function status(Domain $domain)
    {
        $this->authorize('view', $domain);

        // Fetch History for Chart
        $history = $domain->history()
            ->whereNotNull('pagespeed_score_desktop')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->reverse();

        return response()->json([
            'pagespeed_mobile' => $domain->pagespeed_score,
            'pagespeed_desktop' => $domain->pagespeed_score_desktop,
            'updated_at' => $domain->updated_at->toIso8601String(),
            'details' => $domain->last_pagespeed_details,
            'history_labels' => $history->map(fn($h) => $h->created_at->setTimezone('Europe/Berlin')->format('d.m. H:i'))->values(),
            'history_scores' => $history->pluck('pagespeed_score_desktop')->values(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Domain::class);

        $request->validate([
            'url' => 'required|string',
            'keyword_must_contain' => 'nullable|string',
            'keyword_must_not_contain' => 'nullable|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $url = trim($request->url);
        if (!preg_match('#^https?://#', $url)) {
            $url = 'https://' . $url;
        }

        // SSRF Protection
        if (!\App\Services\SecurityService::isSafeUrl($url)) {
            return back()->withErrors(['url' => 'This URL is prohibited for security reasons (internal/private IP).']);
        }

        // Check for duplicates
        if (Domain::where('user_id', $user->id)->where('url', $url)->exists()) {
             return back()->withErrors(['url' => 'You are already monitoring this domain.']);
        }

        $domain = Domain::create([
            'user_id' => $user->id,
            'url' => $url,
            'keyword_must_contain' => $request->keyword_must_contain,
            'keyword_must_not_contain' => $request->keyword_must_not_contain,
        ]);

        // Dispatch Job
        \App\Jobs\PerformSpectoraAudit::dispatchSync($domain);

        return redirect()->route('dashboard')->with('status', 'Domain successfully added!');
    }

    public function destroy(Domain $domain)
    {
        $this->authorize('delete', $domain);
        $domain->delete();

        return redirect()->route('dashboard')->with('status', 'Domain deleted.');
    }
}
