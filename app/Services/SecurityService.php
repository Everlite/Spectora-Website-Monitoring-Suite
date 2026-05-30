<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class SecurityService
{
    /**
     * Pre-configured HTTP client with SSRF connect-time pinning and redirect checks.
     */
    public static function http(): PendingRequest
    {
        return Http::withMiddleware(self::connectTimeMiddleware())
            ->withMiddleware(self::redirectMiddleware());
    }

    /**
     * Checks if a URL points to a private/reserved IP address (SSRF protection).
     */
    public static function isSafeUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! $host) {
            return false;
        }

        if ($host === 'localhost' || $host === 'loopback') {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return self::isSafeIp($host);
        }

        $ips = self::resolveHostIps($host);

        if ($ips === []) {
            return false;
        }

        foreach ($ips as $ip) {
            if (! self::isSafeIp($ip)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if an IP is public and safe.
     */
    public static function isSafeIp(string $ip): bool
    {
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }

        if ($ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '127.')) {
            return false;
        }

        return true;
    }

    /**
     * Build CURLOPT_RESOLVE entries so the TCP connect uses only pre-validated IPs.
     *
     * @return list<string>
     */
    public static function resolvePinsForUrl(string $url): array
    {
        if (! self::isSafeUrl($url)) {
            throw new \RuntimeException('SSRF Protection: Blocked unsafe URL: '.$url);
        }

        $host = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME) ?: 'https';
        $port = parse_url($url, PHP_URL_PORT) ?? ($scheme === 'https' ? 443 : 80);

        $ips = filter_var($host, FILTER_VALIDATE_IP)
            ? [$host]
            : self::resolveHostIps($host);

        $pins = [];
        foreach ($ips as $ip) {
            if (! self::isSafeIp($ip)) {
                throw new \RuntimeException('SSRF Protection: Blocked unsafe IP: '.$ip);
            }
            $pins[] = $host.':'.$port.':'.$ip;
        }

        return $pins;
    }

    /**
     * Guzzle middleware: validate URL and pin DNS at connect time.
     */
    public static function connectTimeMiddleware(): callable
    {
        return function (callable $handler) {
            return function (\Psr\Http\Message\RequestInterface $request, array $options) use ($handler) {
                $url = (string) $request->getUri();
                $pins = self::resolvePinsForUrl($url);

                $curl = $options['curl'] ?? [];
                $existing = $curl[CURLOPT_RESOLVE] ?? [];
                if (! is_array($existing)) {
                    $existing = $existing ? [$existing] : [];
                }
                $curl[CURLOPT_RESOLVE] = array_values(array_unique(array_merge($existing, $pins)));
                $options['curl'] = $curl;

                return $handler($request, $options);
            };
        };
    }

    /**
     * Guzzle middleware to prevent SSRF in redirects.
     */
    public static function redirectMiddleware(): callable
    {
        return function (callable $handler) {
            return function (\Psr\Http\Message\RequestInterface $request, array $options) use ($handler) {
                if (! empty($options['allow_redirects'])) {
                    $options['allow_redirects']['on_redirect'] = function (
                        \Psr\Http\Message\RequestInterface $req,
                        \Psr\Http\Message\ResponseInterface $res,
                        \Psr\Http\Message\UriInterface $uri
                    ) {
                        $redirectUrl = (string) $uri;
                        if (! self::isSafeUrl($redirectUrl)) {
                            throw new \RuntimeException('SSRF Protection: Blocked redirect to unsafe URL: '.$redirectUrl);
                        }
                    };
                }

                return $handler($request, $options);
            };
        };
    }

    /**
     * @return list<string>
     */
    private static function resolveHostIps(string $host): array
    {
        $ips = [];

        try {
            $aRecords = dns_get_record($host, DNS_A) ?: [];
            $aaaaRecords = dns_get_record($host, DNS_AAAA) ?: [];

            foreach (array_merge($aRecords, $aaaaRecords) as $record) {
                $ip = $record['ip'] ?? ($record['ipv6'] ?? null);
                if ($ip) {
                    $ips[] = $ip;
                }
            }
        } catch (\Exception $e) {
            $ip = gethostbyname($host);
            if ($ip !== $host) {
                $ips[] = $ip;
            }
        }

        return array_values(array_unique($ips));
    }
}
