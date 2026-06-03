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
        private readonly GeoResolutionService $geoResolution
    ) {}

    /**
     * Store a new analytics event.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain' => 'required|uuid|exists:domains,uuid',
            'url' => 'required|url',
            'referrer' => 'nullable|string',
            'width' => 'nullable|integer',
        ]);

        $domain = Domain::where('uuid', $validated['domain'])->firstOrFail();

        $origin = $request->header('Origin');
        $referer = $request->header('Referer');
        $expectedHost = HostHelper::fromUrl($domain->url);

        $isLocal = ($origin && (str_contains($origin, 'localhost') || str_contains($origin, '127.0.0.1')))
            || ($referer && (str_contains($referer, 'localhost') || str_contains($referer, '127.0.0.1')));

        if (! $isLocal) {
            $originHost = $origin ? parse_url($origin, PHP_URL_HOST) : null;
            $refererHost = $referer ? parse_url($referer, PHP_URL_HOST) : null;

            $isAuthorized = HostHelper::matches($originHost, $expectedHost)
                || (! $originHost && HostHelper::matches($refererHost, $expectedHost));

            if (! $isAuthorized) {
                abort(403, 'Unauthorized tracking origin (Expected: '.$expectedHost.')');
            }
        }

        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? 'Unknown';
        $date = now()->format('Y-m-d');
        $dailyKey = hash_hmac('sha256', $date, (string) config('app.key'));
        $ipForHash = $ip ? AnalyticsIpService::anonymizeForHash($ip) : '';
        $visitorHash = hash_hmac('sha256', $ipForHash.'|'.$userAgent, $dailyKey);

        $urlPath = parse_url($validated['url'], PHP_URL_PATH) ?? '/';

        $referrerDomain = null;
        if (! empty($validated['referrer'])) {
            $referrerDomain = parse_url($validated['referrer'], PHP_URL_HOST);
        }

        $width = $request->input('width');
        $device = 'desktop';
        if ($width && $width < 768) {
            $device = 'mobile';
        } elseif ($width && $width < 1024) {
            $device = 'tablet';
        }

        $browser = AnalyticsUserAgent::browser($userAgent);
        $os = AnalyticsUserAgent::os($userAgent);
        $precision = $domain->analytics_geo_precision ?? Domain::GEO_CITY;
        $geo = $this->geoResolution->resolve($ip, $request, $precision);

        AnalyticsVisit::create([
            'domain_id' => $domain->id,
            'visitor_hash' => $visitorHash,
            'url' => $validated['url'],
            'path' => $urlPath,
            'referrer' => $validated['referrer'] ?? null,
            'referrer_domain' => $referrerDomain,
            'browser' => $browser,
            'os' => $os,
            'device' => $device,
            'country' => $geo['country'],
            'region' => $geo['region'],
            'city' => $geo['city'],
        ]);

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
            return response()->json(['message' => 'Analytics settings saved.']);
        }

        return back()->with('status', 'Analytics settings saved.');
    }
}
