<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsVisit;
use App\Models\Domain;
use App\Services\AnalyticsIpService;
use App\Services\AnalyticsQueryService;
use App\Services\GeoResolutionService;
use App\Support\AnalyticsUserAgent;
use App\Support\HostHelper;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsQueryService $analyticsQuery,
        private readonly GeoResolutionService $geoResolution,
        private readonly \App\SpectoraEngine\Pulse\PulseIngestEngine $pulseIngest
    ) {}

    /**
     * Store a new pulse / analytics event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain' => 'required|uuid|exists:domains,uuid',
            'url' => 'required|url',
            'referrer' => 'nullable|string',
            'width' => 'nullable|integer',
            'event_type' => 'nullable|string|max:32',
            'event_name' => 'nullable|string|max:64',
            'event_data' => 'nullable|array',
        ]);

        $event = \App\SpectoraEngine\Pulse\PulseEvent::fromRequest($validated);
        $this->pulseIngest->ingest($request, $event);

        return response()->noContent();
    }

    public function show(Domain $domain)
    {
        $this->authorize('view', $domain);

        $metrics = $this->analyticsQuery->getDashboardData($domain);

        return view('domains.analytics', array_merge(
            ['domain' => $domain],
            $metrics
        ));
    }

    public function updateSettings(Request $request, Domain $domain)
    {
        $this->authorize('update', $domain);

        $validated = $request->validate([
            'analytics_geo_precision' => 'required|in:off,country,city',
        ]);

        $domain->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Standort-Genauigkeit gespeichert.']);
        }

        return back()->with('status', 'Standort-Genauigkeit gespeichert.');
    }
}
