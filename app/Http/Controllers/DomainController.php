<?php

namespace App\Http\Controllers;

use App\Jobs\CheckDomainJob;
use App\Jobs\PerformSpectoraAudit;
use App\Models\Domain;
use App\Models\User;
use App\Services\AnalyticsQueryService;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DomainController extends Controller
{
    public function __construct(
        private readonly AnalyticsQueryService $analyticsQuery
    ) {}

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

        $labels = $chartData->map(function ($check) {
            $date = $check->checked_at ?? $check->created_at;
            return $date ? \Carbon\Carbon::parse($date)->format('d.m. H:i') : '--';
        })->values();
        $dataPoints = $chartData->pluck('response_time')->values();

        return view('domains.history', compact('domain', 'checks', 'labels', 'dataPoints', 'showOnlyErrors', 'dateFilter'));
    }

    public function show(Domain $domain)
    {
        $this->authorize('view', $domain);

        $domain->loadMissing('user');

        // Auto-assign UUID if null on existing database row
        if (empty($domain->uuid)) {
            $domain->uuid = (string) Str::uuid();
            $domain->saveQuietly();
        }

        try {
            $analytics = $this->analyticsQuery->getDashboardData($domain);
        } catch (\Throwable $e) {
            Log::warning('Analytics query error: ' . $e->getMessage());
            $analytics = [
                'chartLabels' => [],
                'chartVisitors' => [],
                'chartPageviews' => [],
                'topPages' => collect([]),
                'topSources' => collect([]),
                'deviceLabels' => collect([]),
                'deviceData' => collect([]),
                'deviceStats' => ['desktop' => 0, 'mobile' => 0, 'tablet' => 0],
                'visitsPerDay' => collect([]),
                'topCountries' => collect([]),
                'topCities' => collect([]),
            ];
        }

        // --- 2. History & KPIs ---
        try {
            $uptime = $domain->calculateUptime(30);
        } catch (\Throwable $e) {
            $uptime = 100.0;
        }

        // Uptime History for Sparkline (Last 7 days)
        $uptimeHistory = [];
        for ($i = 6; $i >= 0; $i--) {
            try {
                $date = now()->subDays($i)->format('Y-m-d');
                $dayQuery = $domain->uptimeHistory()->whereDate('created_at', $date);
                $dayTotal = (clone $dayQuery)->count();
                if ($dayTotal === 0) {
                    $uptimeHistory[] = 100;
                } else {
                    $dayFailed = (clone $dayQuery)->failedUptime()->count();
                    $uptimeHistory[] = round((($dayTotal - $dayFailed) / $dayTotal) * 100, 1);
                }
            } catch (\Throwable $e) {
                $uptimeHistory[] = 100;
            }
        }

        // Avg Response Time (24h)
        try {
            $avgResponseTime = $domain->uptimeHistory()
                ->where('created_at', '>=', now()->subDay())
                ->avg('response_time');
            $avgResponseTime = round(($avgResponseTime ?? 0) * 1000);
        } catch (\Throwable $e) {
            $avgResponseTime = 0;
        }

        // Recent Checks (Logbook)
        try {
            $recentChecks = $domain->uptimeHistory()->orderBy('created_at', 'desc')->paginate(20);
        } catch (\Throwable $e) {
            $recentChecks = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        // Monitored URLs for the Overview tab
        try {
            $monitoredUrls = $domain->monitoredUrls()->where('is_active', true)->get();
        } catch (\Throwable $e) {
            $monitoredUrls = collect([]);
        }

        // History Chart (Response Time)
        try {
            $historyChartData = $domain->uptimeHistory()->orderBy('created_at', 'desc')->take(50)->get()->reverse();
            $historyLabels = $historyChartData->map(function ($h) {
                $date = $h->checked_at ?? $h->created_at;
                return $date ? \Carbon\Carbon::parse($date)->format('d.m. H:i') : '--';
            })->values();
            $historyResponseTimes = $historyChartData->map(fn ($h) => round(($h->response_time ?? 0) * 1000))->values();
        } catch (\Throwable $e) {
            $historyLabels = collect([]);
            $historyResponseTimes = collect([]);
        }

        // --- 3. Performance & Security ---
        $sslDaysRemaining = $domain->ssl_days_left ?? 0;

        try {
            $psHistory = $domain->history()
                ->whereNotNull('pagespeed_score_desktop')
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get()
                ->reverse();
            $psHistoryLabels = $psHistory->map(function ($h) {
                $date = $h->checked_at ?? $h->created_at;
                return $date ? \Carbon\Carbon::parse($date)->format('d.m.') : '--';
            })->values();
            $psHistoryScores = $psHistory->pluck('pagespeed_score_desktop')->values();
        } catch (\Throwable $e) {
            $psHistoryLabels = collect([]);
            $psHistoryScores = collect([]);
        }

        $score = $domain->pagespeed_score_desktop ?? 0;
        $scoreColor = $score >= 90 ? 'emerald' : ($score >= 50 ? 'amber' : 'rose');

        $watchdogData = $domain->safety_details['watchdog'] ?? [];

        // --- 4. Notes ---
        try {
            $notes = $domain->notes()
                ->with('user:id,first_name,last_name,email')
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Throwable $e) {
            $notes = collect([]);
        }

        // --- 5. Security & Audit Summary ---
        $auditDetails = $domain->last_pagespeed_details ?? [];
        $criticalCount = collect($auditDetails)->where('status', 'error')->count();
        $warningCount = collect($auditDetails)->where('status', 'warning')->count();
        $securityIssues = $auditDetails;
        $topReferrers = $analytics['topSources'] ?? [];

        // All Monitored Domains for Quick Target Switcher in Header
        $allDomains = Domain::where('user_id', Auth::id())->select('id', 'uuid', 'url', 'status_code')->get();

        return view('domains.dashboard', array_merge(
            compact(
                'domain',
                'allDomains',
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
                'securityIssues',
                'topReferrers',
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
        PerformSpectoraAudit::dispatchSync($domain);
        CheckDomainJob::dispatchSync($domain, synchronous: true);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Analysis started']);
        }

        return back()->with('status', 'Spectora audit started. Refresh in a few seconds.');
    }

    public function status(Domain $domain)
    {
        $this->authorize('view', $domain);

        $history = $domain->history()
            ->whereNotNull('pagespeed_score_desktop')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->reverse();

        return response()->json([
            'pagespeed_mobile' => $domain->pagespeed_score,
            'pagespeed_desktop' => $domain->pagespeed_score_desktop,
            'updated_at' => $domain->updated_at?->toIso8601String(),
            'details' => $domain->last_pagespeed_details,
            'history_labels' => $history->map(function ($h) {
                $date = $h->checked_at ?? $h->created_at;
                return $date ? \Carbon\Carbon::parse($date)->setTimezone('Europe/Berlin')->format('d.m. H:i') : '--';
            })->values(),
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

        $url = $request->url;
        if (! preg_match('~^(?:f|ht)tps?://~i', $url)) {
            $url = 'https://'.$url;
        }

        $parsed = parse_url($url);
        $cleanUrl = ($parsed['scheme'] ?? 'https').'://'.($parsed['host'] ?? $url);

        $domain = Domain::create([
            'user_id' => Auth::id(),
            'uuid' => (string) Str::uuid(),
            'url' => $cleanUrl,
            'keyword_must_contain' => $request->keyword_must_contain,
            'keyword_must_not_contain' => $request->keyword_must_not_contain,
            'status_code' => 0,
            'ssl_days_left' => 0,
            'response_time' => 0,
            'safety_status' => 'safe',
        ]);

        // Immediate first check
        CheckDomainJob::dispatchSync($domain, synchronous: true);
        PerformSpectoraAudit::dispatchSync($domain);

        return redirect()->route('dashboard')->with('status', 'Website '.$cleanUrl.' erfolgreich hinzugefügt und geprüft.');
    }

    public function destroy(Domain $domain)
    {
        $this->authorize('delete', $domain);

        $url = $domain->url;
        $domain->delete();

        return redirect()->route('dashboard')->with('status', 'Website '.$url.' erfolgreich entfernt.');
    }
}
