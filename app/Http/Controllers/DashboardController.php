<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsVisit;
use App\Models\ChecksHistory;
use App\Models\Domain;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $domainsQuery = Domain::query();
        if (! $user->is_admin) {
            $domainsQuery->where('user_id', $user->id);
        }

        $domains = $domainsQuery->orderBy('url')->get();
        $domainIds = $domains->pluck('id')->all();

        // 1. Batch Compute 30-Day Uptimes (0 N+1 Queries)
        $thirtyDaysAgo = now()->subDays(30);
        $uptimeStats = [];
        if (! empty($domainIds)) {
            $rawChecks = ChecksHistory::query()
                ->whereIn('domain_id', $domainIds)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->whereNotNull('response_time')
                ->selectRaw('domain_id, 
                    COUNT(*) as total_checks, 
                    SUM(CASE WHEN (status_code >= 400 OR status_code = 0) THEN 1 ELSE 0 END) as failed_checks,
                    AVG(response_time) as avg_rt')
                ->groupBy('domain_id')
                ->get()
                ->keyBy('domain_id');

            foreach ($domains as $domain) {
                $stat = $rawChecks->get($domain->id);
                if ($stat && $stat->total_checks > 0) {
                    $domain->calculated_uptime = round((($stat->total_checks - $stat->failed_checks) / $stat->total_checks) * 100, 1);
                    $domain->avg_response_time_ms = round(($stat->avg_rt ?? 0) * 1000);
                } else {
                    $domain->calculated_uptime = ($domain->status_code >= 200 && $domain->status_code < 400) ? 100.0 : 0.0;
                    $domain->avg_response_time_ms = round(($domain->response_time ?? 0) * 1000);
                }
            }

            // 2. Batch Compute 24h Unique Visitors
            $todayVisitors = AnalyticsVisit::query()
                ->whereIn('domain_id', $domainIds)
                ->where('created_at', '>=', now()->startOfDay())
                ->selectRaw('domain_id, COUNT(DISTINCT visitor_hash) as unique_visitors')
                ->groupBy('domain_id')
                ->pluck('unique_visitors', 'domain_id');

            foreach ($domains as $domain) {
                $domain->visitors_count_today = (int) ($todayVisitors->get($domain->id) ?? 0);
            }
        }

        // 3. Compute Global Agency Health KPIs
        $totalWebsites = $domains->count();
        $onlineCount = $domains->filter(fn ($d) => $d->status_code >= 200 && $d->status_code < 400)->count();
        $activeIncidents = $domains->filter(fn ($d) => ($d->status_code >= 400 || $d->status_code === 0 || $d->safety_status === 'danger'))->count();
        $globalUptime = $totalWebsites > 0
            ? round($domains->avg('calculated_uptime'), 1)
            : 100.0;
        $totalVisitorsToday = $domains->sum('visitors_count_today');

        $kpis = [
            'total_websites' => $totalWebsites,
            'online_count' => $onlineCount,
            'active_incidents' => $activeIncidents,
            'global_uptime' => $globalUptime,
            'total_visitors_today' => $totalVisitorsToday,
        ];

        return view('dashboard', compact('domains', 'kpis'));
    }
}
