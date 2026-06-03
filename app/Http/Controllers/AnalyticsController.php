<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsVisit;
use App\Models\Domain;
use App\Services\AnalyticsQueryService;
use App\Support\HostHelper;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsQueryService $analyticsQuery
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

        $visitorHash = hash_hmac('sha256', $ip.'|'.$userAgent, $dailyKey);

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

        $browser = $this->getBrowser($userAgent);
        $os = $this->getOs($userAgent);
        $country = env('TRUSTED_PROXIES')
            ? $request->header('CF-IPCountry')
            : null;

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
            'country' => $country,
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

    private function getBrowser(string $ua): string
    {
        $ua = strtolower($ua);
        if (str_contains($ua, 'chrome')) {
            return 'Chrome';
        }
        if (str_contains($ua, 'firefox')) {
            return 'Firefox';
        }
        if (str_contains($ua, 'safari') && ! str_contains($ua, 'chrome')) {
            return 'Safari';
        }
        if (str_contains($ua, 'edge')) {
            return 'Edge';
        }
        if (str_contains($ua, 'opera') || str_contains($ua, 'opr')) {
            return 'Opera';
        }

        return 'Other';
    }

    private function getOs(string $ua): string
    {
        $ua = strtolower($ua);
        if (str_contains($ua, 'windows')) {
            return 'Windows';
        }
        if (str_contains($ua, 'mac os')) {
            return 'macOS';
        }
        if (str_contains($ua, 'android')) {
            return 'Android';
        }
        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipad')) {
            return 'iOS';
        }
        if (str_contains($ua, 'linux')) {
            return 'Linux';
        }

        return 'Other';
    }
}
