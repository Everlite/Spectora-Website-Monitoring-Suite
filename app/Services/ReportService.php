<?php

namespace App\Services;

use App\Models\Domain;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    public function generatePdf(Domain $domain)
    {
        $user = $domain->user;
        
        // Determine logo path and encode as Base64
        $logoBase64 = null;
        $logoPath = null;
        
        if ($user->agency_logo_path) {
            $logoPath = public_path('storage/' . $user->agency_logo_path);
        } else {
            $logoPath = public_path('images/logo.png');
        }

        if ($logoPath && file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // Gather Data
        // 1. Real Uptime Calculation (Last 30 Days)
        $totalChecks = $domain->history()->where('created_at', '>=', now()->subDays(30))->count();
        $failedChecks = $domain->history()
            ->where('created_at', '>=', now()->subDays(30))
            ->where(function($q) {
                $q->where('status_code', '>=', 400)
                  ->orWhereNull('status_code')
                  ->orWhere('status_code', 0);
            })->count();
        
        $uptime = $totalChecks > 0 
            ? number_format((($totalChecks - $failedChecks) / $totalChecks) * 100, 1) . '%' 
            : '0.0%';

        // 2. Avg Response Time
        $avgResponseTime = $domain->response_time ?? 0;

        // 3. Visitors (Last 30 Days)
        $visitors = $domain->analyticsVisits()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        
        // 4. Security Status
        $securityStatus = ucfirst($domain->safety_status ?? 'Unknown');

        // Parse safety details into a flat array of strings for the report
        $safetyIssues = [];
        $rawSafetyDetails = $domain->safety_details ?? [];
        if (isset($rawSafetyDetails['keywords_found'])) {
            foreach ((array)$rawSafetyDetails['keywords_found'] as $kw) {
                $safetyIssues[] = "Forbidden keyword found: " . $kw;
            }
        }
        if (isset($rawSafetyDetails['watchdog']['issues'])) {
            foreach ((array)$rawSafetyDetails['watchdog']['issues'] as $issue) {
                $title = $issue['title'] ?? 'Security Issue';
                $desc = $issue['description'] ?? '';
                $safetyIssues[] = $title . ($desc ? ': ' . $desc : '');
            }
        }

        $data = [
            'domain' => $domain,
            'logoBase64' => $logoBase64,
            'uptime' => $uptime,
            'avgResponseTime' => $avgResponseTime,
            'visitors' => $visitors,
            'securityStatus' => $securityStatus,
            'safetyDetails' => $safetyIssues,
            'date' => now()->format('F Y'),
        ];

        // --- Real Data for Charts (Last 30 Days) ---
        $days = collect(range(29, 0))->map(fn($days) => now()->subDays($days)->format('Y-m-d'));
        $labels = $days->map(fn($date) => \Carbon\Carbon::parse($date)->format('d.m'));

        // Helper to fetch and encode image with SSRF Middleware
        $fetchChart = function($config) {
            $url = 'https://quickchart.io/chart?c=' . urlencode(json_encode($config)) . '&w=400&h=200';
            
            if (!\App\Services\SecurityService::isSafeUrl($url)) {
                return null;
            }

            try {
                $response = \Illuminate\Support\Facades\Http::withMiddleware(\App\Services\SecurityService::redirectMiddleware())
                    ->timeout(10)
                    ->get($url);

                if ($response->successful()) {
                    return 'data:image/png;base64,' . base64_encode($response->body());
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("QuickChart fetch failed: " . $e->getMessage());
            }
            return null;
        };

        // 1. Performance (Response Time) - Line Chart
        // Fetch from history
        $responseHistory = $domain->history()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, AVG(response_time) as avg_time')
            ->groupBy('date')
            ->pluck('avg_time', 'date');

        $performanceData = $days->map(fn($date) => $responseHistory->get($date) ?? null); // Null for gaps
        
        $chartConfig1 = [
            'type' => 'line',
            'data' => [
                'labels' => $labels->toArray(),
                'datasets' => [[
                    'label' => 'Response Time (s)',
                    'data' => $performanceData->toArray(),
                    'borderColor' => '#38BDF8', // Cyan
                    'backgroundColor' => 'rgba(56, 189, 248, 0.1)',
                    'fill' => true,
                    'pointRadius' => 2,
                    'spanGaps' => true, // Connect lines over gaps
                ]]
            ],
            'options' => [
                'legend' => ['display' => false],
                'scales' => [
                    'xAxes' => [['display' => false]],
                    'yAxes' => [['display' => true, 'ticks' => ['beginAtZero' => true]]]
                ]
            ]
        ];
        $data['chartResponse'] = $fetchChart($chartConfig1);

        // 2. Visitors - Bar Chart
        $visits = $domain->analyticsVisits()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(DISTINCT visitor_hash) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $visitorData = $days->map(fn($date) => $visits->get($date) ?? 0); // 0 for missing days
        
        $chartConfig2 = [
            'type' => 'bar',
            'data' => [
                'labels' => $labels->toArray(),
                'datasets' => [[
                    'label' => 'Visitors',
                    'data' => $visitorData->toArray(),
                    'backgroundColor' => '#0F172A', // Dark Blue
                ]]
            ],
            'options' => [
                'legend' => ['display' => false],
                'scales' => [
                    'xAxes' => [['display' => false]],
                    'yAxes' => [['display' => false, 'ticks' => ['beginAtZero' => true]]]
                ]
            ]
        ];
        $data['chartVisitors'] = $fetchChart($chartConfig2);

        // 3. Score Trend - Line Chart (Replacing Gauge)
        $scoreHistory = $domain->history()
            ->where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('pagespeed_score_desktop')
            ->selectRaw('DATE(created_at) as date, AVG(pagespeed_score_desktop) as score')
            ->groupBy('date')
            ->pluck('score', 'date');

        $scoreData = $days->map(fn($date) => $scoreHistory->get($date) ?? null);

        $chartConfig3 = [
            'type' => 'line',
            'data' => [
                'labels' => $labels->toArray(),
                'datasets' => [[
                    'label' => 'Spectora Score',
                    'data' => $scoreData->toArray(),
                    'borderColor' => '#10B981', // Green
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'pointRadius' => 2,
                    'spanGaps' => true,
                ]]
            ],
            'options' => [
                'legend' => ['display' => false],
                'scales' => [
                    'xAxes' => [['display' => false]],
                    'yAxes' => [['display' => true, 'ticks' => ['min' => 0, 'max' => 100]]]
                ]
            ]
        ];
        $data['chartScore'] = $fetchChart($chartConfig3);

        // --- Real Data for "Recent Checks" Table ---
        $data['recentChecks'] = $domain->history()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($check) {
                $status = ($check->status_code >= 200 && $check->status_code < 400) ? 'Online' : 'Offline';
                if ($check->status_code === 0) $status = 'Error';
                
                return [
                    'check' => 'Uptime Check',
                    'status' => $status . ' (HTTP ' . ($check->status_code ?: '???') . ')',
                    'time' => $check->created_at->format('H:i d.m.')
                ];
            })->toArray();

        $pdf = Pdf::loadView('reports.monthly', $data);
        $pdf->setOptions(['isRemoteEnabled' => true, 'defaultFont' => 'sans-serif']);
        
        return $pdf;
    }
}
