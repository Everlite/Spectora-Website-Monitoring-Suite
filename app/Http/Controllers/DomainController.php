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

        $labels = $chartData->map(fn ($check) => $check->created_at->format('d.m. H:i'))->values();
        $dataPoints = $chartData->pluck('response_time')->values();

        return view('domains.history', compact('domain', 'checks', 'labels', 'dataPoints', 'showOnlyErrors', 'dateFilter'));
    }

    public function show(Domain $domain)
    {
        $this->authorize('view', $domain);

        $domain->loadMissing('user');

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
        $historyLabels = $historyChartData->map(fn ($h) => $h->created_at->format('d.m. H:i'))->values();
        $historyResponseTimes = $historyChartData->map(fn ($h) => round($h->response_time * 1000))->values();

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
        $psHistoryLabels = $psHistory->map(fn ($h) => $h->created_at->format('d.m.'))->values();
        $psHistoryScores = $psHistory->pluck('pagespeed_score_desktop')->values();

        // Main Performance Score
        $score = $domain->pagespeed_score_desktop ?? 0;
        $scoreColor = $score >= 90 ? 'emerald' : ($score >= 50 ? 'amber' : 'rose');

        // Watchdog / Security
        $watchdogData = $domain->safety_details['watchdog'] ?? [];

        // --- 4. Notes ---
        $notes = $domain->notes()
            ->with('user:id,first_name,last_name,email')
            ->orderBy('created_at', 'desc')
            ->get();

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
            'history_labels' => $history->map(fn ($h) => $h->created_at->setTimezone('Europe/Berlin')->format('d.m. H:i'))->values(),
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

        /** @var User $user */
        $user = Auth::user();

        $url = trim($request->url);
        if (! preg_match('#^https?://#', $url)) {
            $url = 'https://'.$url;
        }

        // SSRF Protection
        if (! SecurityService::isSafeUrl($url)) {
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
        PerformSpectoraAudit::dispatchSync($domain);

        return redirect()->route('dashboard')->with('status', 'Domain successfully added!');
    }

    public function destroy(Domain $domain)
    {
        $this->authorize('delete', $domain);
        $domain->delete();

        return redirect()->route('dashboard')->with('status', 'Domain deleted.');
    }
}
