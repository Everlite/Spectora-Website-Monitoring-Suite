<?php

namespace App\SpectoraEngine\Pulse;

use App\Models\AnalyticsVisit;
use App\Models\Domain;
use App\Services\GeoResolutionService;
use App\Support\AnalyticsUserAgent;
use App\Support\HostHelper;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PulseIngestEngine
{
    public function __construct(
        private readonly GeoResolutionService $geoResolution
    ) {}

    /**
     * Ingests a telemetry/pulse event from client website.
     *
     * @throws ValidationException
     */
    public function ingest(Request $request, PulseEvent $event): AnalyticsVisit
    {
        $domain = Domain::where('uuid', $event->domainUuid)->first();
        if (! $domain) {
            throw ValidationException::withMessages(['domain' => 'Domain not found.']);
        }

        // Origin and Referer verification
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
                abort(403, 'Unauthorized pulse origin (Expected: '.$expectedHost.')');
            }
        }

        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? 'Unknown';
        $visitorHash = PulseAnonymizer::generateVisitorHash($ip, $userAgent);

        $referrerDomain = null;
        if (! empty($event->referrer)) {
            $referrerDomain = parse_url($event->referrer, PHP_URL_HOST);
        }

        // Determine device category
        $device = 'desktop';
        if ($event->width && $event->width < 768) {
            $device = 'mobile';
        } elseif ($event->width && $event->width < 1024) {
            $device = 'tablet';
        }

        $browser = AnalyticsUserAgent::browser($userAgent);
        $os = AnalyticsUserAgent::os($userAgent);
        $precision = $domain->analytics_geo_precision ?? Domain::GEO_CITY;
        $geo = $this->geoResolution->resolve($ip, $request, $precision);

        return AnalyticsVisit::create([
            'domain_id' => $domain->id,
            'visitor_hash' => $visitorHash,
            'url' => $event->url,
            'path' => $event->path ?? '/',
            'referrer' => $event->referrer,
            'referrer_domain' => $referrerDomain,
            'browser' => $browser,
            'os' => $os,
            'device' => $device,
            'country' => $geo['country'],
            'region' => $geo['region'],
            'city' => $geo['city'],
        ]);
    }
}
