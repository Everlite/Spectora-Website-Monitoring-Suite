<?php

namespace App\SpectoraEngine;

use App\Models\ChecksHistory;
use App\Models\Domain;
use App\Models\MonitoredUrl;
use App\Services\MonitoringFilterService;
use App\Services\SecurityService;
use App\SpectoraEngine\Incidents\IncidentStateMachine;
use App\SpectoraEngine\Watchdog\SpectoraWatchdogEngine;
use Illuminate\Support\Facades\Log;

class SpectoraEngine
{
    public function __construct(
        private readonly MonitoringFilterService $filter,
        private readonly SpectoraWatchdogEngine $watchdog,
        private readonly IncidentStateMachine $incidents,
    ) {}

    public function probe(Domain $domain, ?MonitoredUrl $monitoredUrl = null, ?string $url = null): ProbeResult
    {
        $url = $url ?? ($monitoredUrl?->url ?? $domain->url);

        if (! str_starts_with($url, 'http')) {
            $url = 'https://'.$url;
        }

        $preCheck = $this->filter->shouldCheck($domain, $url);
        if (! $preCheck['should_check']) {
            Log::info("Skipping check for {$url}: {$preCheck['reason']}");

            return new ProbeResult(ran: false, skipReason: $preCheck['reason']);
        }

        if (! SecurityService::resolve()->isSafeUrl($url)) {
            Log::warning("SSRF Protection: Blocked prohibited URL check for {$url}");

            return new ProbeResult(ran: false, skipReason: 'ssrf_blocked');
        }

        $startTime = microtime(true);
        $statusCode = 0;
        $sslDays = null;
        $issues = [];
        $safetyStatus = 'safe';
        $safetyDetails = [];
        $responseTime = null;

        try {
            $response = SecurityService::resolve()->httpClient()
                ->withUserAgent('SpectoraBot/2.0 (Spectora Engine Probe; +'.rtrim((string) config('app.url'), '/').')')
                ->timeout(15)
                ->get($url);

            $postFilter = $this->filter->shouldIgnoreResponse($domain, $response);
            if ($postFilter['ignore']) {
                Log::info("Ignoring response for {$url}: {$postFilter['reason']}");
                if ($monitoredUrl) {
                    $monitoredUrl->update(['last_safety_status' => 'ignored']);
                }

                return new ProbeResult(ran: false, skipReason: $postFilter['reason']);
            }

            $statusCode = $response->status();
            $responseTime = microtime(true) - $startTime;
            $rawBody = $response->body();
            $body = strtolower($rawBody);

            $sslDays = $this->sslDaysLeft($url);
            $this->applyKeywordRules($domain, $body, $issues, $safetyStatus, $safetyDetails);
            $this->applyWatchdog($domain, $url, $rawBody, $statusCode, $issues, $safetyStatus, $safetyDetails);

            if ($statusCode >= 400 || $statusCode === 0) {
                $issues[] = "❌ Unreachable (HTTP $statusCode)";
            }
        } catch (\Exception $e) {
            $responseTime = microtime(true) - $startTime;
            Log::error("Check failed for {$url}: ".$e->getMessage());
            $statusCode = 0;
            $issues[] = '❌ Check failed: '.$e->getMessage();
            $safetyStatus = 'danger';
        }

        $this->persist($domain, $monitoredUrl, $statusCode, $responseTime ?? 0, $sslDays, $safetyStatus, $safetyDetails);
        $this->incidents->transition($domain, $monitoredUrl, $issues, $url);
        SecurityService::clearHostIpCache();

        return new ProbeResult(
            ran: true,
            statusCode: $statusCode,
            responseTime: $responseTime,
            sslDays: $sslDays,
            safetyStatus: $safetyStatus,
            issues: $issues,
            safetyDetails: $safetyDetails,
        );
    }

    /**
     * @param  list<string>  $issues
     * @param  array<string, mixed>  $safetyDetails
     */
    private function applyKeywordRules(Domain $domain, string $body, array &$issues, string &$safetyStatus, array &$safetyDetails): void
    {
        if ($domain->keyword_must_contain) {
            foreach (array_map('trim', explode(',', $domain->keyword_must_contain)) as $keyword) {
                if ($keyword !== '' && ! str_contains($body, strtolower($keyword))) {
                    $issues[] = '❌ Required keyword missing: '.htmlspecialchars($keyword);
                    $safetyStatus = 'danger';
                    $safetyDetails['keywords_missing'][] = $keyword;
                }
            }
        }

        if ($domain->keyword_must_not_contain) {
            foreach (array_map('trim', explode(',', $domain->keyword_must_not_contain)) as $keyword) {
                if ($keyword !== '' && str_contains($body, strtolower($keyword))) {
                    $issues[] = '❌ Error keyword found: '.htmlspecialchars($keyword);
                    $safetyStatus = 'danger';
                    $safetyDetails['keywords_found'][] = $keyword;
                }
            }
        }
    }

    /**
     * @param  list<string>  $issues
     * @param  array<string, mixed>  $safetyDetails
     */
    private function applyWatchdog(
        Domain $domain,
        string $url,
        string $rawBody,
        int $statusCode,
        array &$issues,
        string &$safetyStatus,
        array &$safetyDetails
    ): void {
        try {
            $scanResult = $this->watchdog->scan($domain, $url, $rawBody, $statusCode);
            $safetyDetails['watchdog'] = $scanResult->toArray();

            if ($scanResult->isDangerous()) {
                $safetyStatus = 'danger';
                foreach ($scanResult->issues as $issue) {
                    if (($issue['severity'] ?? '') === 'critical') {
                        $issues[] = "🚨 {$issue['title']}: {$issue['description']}";
                    }
                }
            } elseif ($scanResult->hasWarnings() && $safetyStatus === 'safe') {
                $safetyStatus = 'warning';
            }
        } catch (\Exception $e) {
            Log::error("Watchdog scan failed for {$url}: ".$e->getMessage());
        }
    }

    /**
     * @param  array<string, mixed>  $safetyDetails
     */
    private function persist(
        Domain $domain,
        ?MonitoredUrl $monitoredUrl,
        int $statusCode,
        float $responseTime,
        ?int $sslDays,
        string $safetyStatus,
        array $safetyDetails
    ): void {
        if ($monitoredUrl) {
            $monitoredUrl->update([
                'last_status_code' => $statusCode,
                'last_safety_status' => $safetyStatus,
                'last_response_time' => round($responseTime * 1000),
                'last_checked' => now(),
            ]);
            $domain->update(['last_checked' => now()]);
        } else {
            Log::info("Updating domain {$domain->url} Status: {$statusCode}, SSL: {$sslDays}");
            try {
                $domain->update([
                    'status_code' => $statusCode,
                    'ssl_days_left' => $sslDays,
                    'response_time' => $responseTime,
                    'safety_status' => $safetyStatus,
                    'safety_details' => $safetyDetails,
                    'last_checked' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to update domain record: '.$e->getMessage());
            }
        }

        ChecksHistory::create([
            'domain_id' => $domain->id,
            'monitored_url_id' => $monitoredUrl?->id,
            'status_code' => $statusCode,
            'response_time' => $responseTime,
            'safety_status' => $safetyStatus,
            'ssl_days_left' => $sslDays,
            'created_at' => now(),
        ]);
    }

    private function sslDaysLeft(string $url): ?int
    {
        try {
            if (! str_starts_with($url, 'http')) {
                $url = 'https://'.$url;
            }

            if (! SecurityService::resolve()->isSafeUrl($url)) {
                return null;
            }

            $host = parse_url($url, PHP_URL_HOST);
            if (! $host) {
                return null;
            }

            $pins = SecurityService::resolve()->resolvePinsForUrl($url);
            if ($pins === []) {
                return null;
            }

            $port = parse_url($url, PHP_URL_PORT) ?? 443;
            $pinSuffix = ':'.$port.':';
            $pinPos = strrpos($pins[0], $pinSuffix);
            if ($pinPos === false) {
                return null;
            }
            $ip = substr($pins[0], $pinPos + strlen($pinSuffix));
            if (! SecurityService::resolve()->isSafeIp($ip)) {
                return null;
            }

            $connectTarget = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
                ? "ssl://[{$ip}]:{$port}"
                : "ssl://{$ip}:{$port}";

            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'peer_name' => $host,
                ],
            ]);

            $client = @stream_socket_client(
                $connectTarget,
                $errno,
                $errstr,
                10,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if (! $client) {
                Log::warning("SSL Socket failed for {$host}: {$errstr} ({$errno})");

                return null;
            }

            $params = stream_context_get_params($client);
            if (! isset($params['options']['ssl']['peer_certificate'])) {
                Log::warning("SSL Certificate capture failed for {$host}");
                fclose($client);

                return null;
            }

            $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
            fclose($client);

            if (! $cert || ! isset($cert['validTo_time_t'])) {
                Log::warning("SSL Certificate parsing failed for {$host}");

                return null;
            }

            return max(0, (int) floor(($cert['validTo_time_t'] - time()) / 86400));
        } catch (\Exception $e) {
            Log::error("SSL check exception for {$url}: ".$e->getMessage());

            return null;
        }
    }
}
