<?php

namespace App\Services;

use App\Models\AnalyticsVisit;
use App\Models\Domain;
use Illuminate\Support\Facades\DB;

class AnalyticsQueryService
{
    /**
     * @return array<string, mixed>
     */
    public function getDashboardData(Domain $domain, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $analyticsData = AnalyticsVisit::where('domain_id', $domain->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, count(*) as pageviews, count(distinct visitor_hash) as visitors')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartData = [];
        $currentDate = $startDate->copy();
        $now = now();
        $analyticsKeyed = $analyticsData->keyBy('date');

        while ($currentDate <= $now) {
            $dateStr = $currentDate->format('Y-m-d');
            $record = $analyticsKeyed->get($dateStr);
            $chartData[] = [
                'date' => $currentDate->format('d.m.'),
                'visitors' => $record ? (int) $record->visitors : 0,
                'pageviews' => $record ? (int) $record->pageviews : 0,
            ];
            $currentDate->addDay();
        }

        $devices = AnalyticsVisit::where('domain_id', $domain->id)
            ->where('created_at', '>=', $startDate)
            ->select('device', DB::raw('count(*) as total'))
            ->groupBy('device')
            ->get();

        $deviceData = $devices->pluck('total');
        $totalDeviceVisits = $deviceData->sum();
        $deviceStats = $devices->mapWithKeys(function ($d) use ($totalDeviceVisits) {
            return [
                strtolower($d->device) => $totalDeviceVisits > 0
                    ? (int) round(($d->total / $totalDeviceVisits) * 100)
                    : 0,
            ];
        })->toArray();

        return [
            'chartLabels' => array_column($chartData, 'date'),
            'chartVisitors' => array_column($chartData, 'visitors'),
            'chartPageviews' => array_column($chartData, 'pageviews'),
            'topPages' => AnalyticsVisit::where('domain_id', $domain->id)
                ->where('created_at', '>=', $startDate)
                ->select('url', DB::raw('count(*) as total'))
                ->groupBy('url')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
            'topSources' => AnalyticsVisit::where('domain_id', $domain->id)
                ->where('created_at', '>=', $startDate)
                ->whereNotNull('referrer_domain')
                ->select('referrer_domain', DB::raw('count(*) as total'))
                ->groupBy('referrer_domain')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),
            'deviceLabels' => $devices->pluck('device'),
            'deviceData' => $deviceData,
            'deviceStats' => $deviceStats,
            'visitsPerDay' => AnalyticsVisit::where('domain_id', $domain->id)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, count(*) as total, count(distinct visitor_hash) as visitors')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }
}
