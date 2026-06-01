<?php

namespace App\Services;

use App\Models\Domain;
use App\Support\ReportChartSvg;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    public function generatePdf(Domain $domain)
    {
        $user = $domain->user;

        $logoBase64 = null;
        $logoPath = $this->resolveReadableLogoPath($user->agency_logo_path);

        if ($logoPath && file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/'.$type.';base64,'.base64_encode($data);
        }

        $uptimeQuery = $domain->uptimeHistory()->where('created_at', '>=', now()->subDays(30));
        $totalChecks = (clone $uptimeQuery)->count();
        $failedChecks = (clone $uptimeQuery)->failedUptime()->count();

        $uptime = $totalChecks > 0
            ? number_format((($totalChecks - $failedChecks) / $totalChecks) * 100, 1).'%'
            : '0.0%';

        $avgResponseTime = $domain->response_time ?? 0;

        $visitors = $domain->analyticsVisits()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $securityStatus = ucfirst($domain->safety_status ?? 'Unknown');

        $safetyIssues = [];
        $rawSafetyDetails = $domain->safety_details ?? [];
        if (isset($rawSafetyDetails['keywords_found'])) {
            foreach ((array) $rawSafetyDetails['keywords_found'] as $kw) {
                $safetyIssues[] = 'Forbidden keyword found: '.$kw;
            }
        }
        if (isset($rawSafetyDetails['keywords_missing'])) {
            foreach ((array) $rawSafetyDetails['keywords_missing'] as $kw) {
                $safetyIssues[] = 'Required keyword missing: '.$kw;
            }
        }
        if (isset($rawSafetyDetails['watchdog']['issues'])) {
            foreach ((array) $rawSafetyDetails['watchdog']['issues'] as $issue) {
                $title = $issue['title'] ?? 'Security Issue';
                $desc = $issue['description'] ?? '';
                $safetyIssues[] = $title.($desc ? ': '.$desc : '');
            }
        }

        $days = collect(range(29, 0))->map(fn ($days) => now()->subDays($days)->format('Y-m-d'));

        $responseHistory = $domain->uptimeHistory()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, AVG(response_time) as avg_time')
            ->groupBy('date')
            ->pluck('avg_time', 'date');

        $performanceData = $days->map(fn ($date) => $responseHistory->get($date) !== null ? (float) $responseHistory->get($date) : null)->values()->all();

        $visits = $domain->analyticsVisits()
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(DISTINCT visitor_hash) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $visitorData = $days->map(fn ($date) => (int) ($visits->get($date) ?? 0))->values()->all();

        $scoreHistory = $domain->history()
            ->where('created_at', '>=', now()->subDays(30))
            ->whereNotNull('pagespeed_score_desktop')
            ->selectRaw('DATE(created_at) as date, AVG(pagespeed_score_desktop) as score')
            ->groupBy('date')
            ->pluck('score', 'date');

        $scoreData = $days->map(fn ($date) => $scoreHistory->get($date) !== null ? (float) $scoreHistory->get($date) : null)->values()->all();

        $data = [
            'domain' => $domain,
            'logoBase64' => $logoBase64,
            'uptime' => $uptime,
            'avgResponseTime' => $avgResponseTime,
            'visitors' => $visitors,
            'securityStatus' => $securityStatus,
            'safetyDetails' => $safetyIssues,
            'date' => now()->format('F Y'),
            'chartResponse' => ReportChartSvg::lineChart($performanceData),
            'chartVisitors' => ReportChartSvg::barChart($visitorData),
            'chartScore' => ReportChartSvg::lineChart($scoreData, stroke: '#10B981', fill: 'rgba(16, 185, 129, 0.15)', minY: 0, maxY: 100),
            'recentChecks' => $domain->uptimeHistory()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($check) {
                    $status = ($check->status_code >= 200 && $check->status_code < 400) ? 'Online' : 'Offline';
                    if ($check->status_code === 0) {
                        $status = 'Error';
                    }

                    return [
                        'check' => 'Uptime Check',
                        'status' => $status.' (HTTP '.($check->status_code ?: '???').')',
                        'time' => $check->created_at->format('H:i d.m.'),
                    ];
                })->toArray(),
        ];

        $pdf = Pdf::loadView('reports.monthly', $data);
        $pdf->setOptions(['isRemoteEnabled' => false, 'defaultFont' => 'sans-serif']);

        return $pdf;
    }

    private function resolveReadableLogoPath(?string $relativePath): string
    {
        $fallback = public_path('images/logo.png');

        if ($relativePath === null || $relativePath === '') {
            return $fallback;
        }

        $storageRoot = realpath(public_path('storage'));
        if ($storageRoot === false) {
            return $fallback;
        }

        $candidate = realpath($storageRoot.DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath));

        if ($candidate !== false && str_starts_with($candidate, $storageRoot.DIRECTORY_SEPARATOR)) {
            return $candidate;
        }

        return $fallback;
    }
}
