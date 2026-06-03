<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class SecurityService
{
    /**
     * Per-resolution DNS cache so validation and CURLOPT_RESOLVE use the same IPs.
     *
     * @var array<string, list<string>>
     */
    private array $hostIpCache = [];

    public function resetHostIpCache(): void
    {
        $this->hostIpCache = [];
    }

    public function httpClient(): PendingRequest
    {
        return Http::withMiddleware($this->buildConnectTimeMiddleware())
            ->withMiddleware($this->buildRedirectMiddleware());
    }

    public function isSafeUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! $host) {
            return false;
        }

        if ($host === 'localhost' || $host === 'loopback') {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $this->isSafeIp($host);
        }

        $ips = $this->resolveHostIpsCached($host);

        if ($ips === []) {
            return false;
        }

        foreach ($ips as $ip) {
            if (! $this->isSafeIp($ip)) {
                return false;
            }
        }

        return true;
    }

    public function isSafeIp(string $ip): bool
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
     * @return list<string>
     */
    public function resolvePinsForUrl(string $url): array
    {
        if (! $this->isSafeUrl($url)) {
            throw new \RuntimeException('SSRF Protection: Blocked unsafe URL: '.$url);
        }

        $host = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME) ?: 'https';
        $port = parse_url($url, PHP_URL_PORT) ?? ($scheme === 'https' ? 443 : 80);

        $ips = filter_var($host, FILTER_VALIDATE_IP)
            ? [$host]
            : $this->resolveHostIpsCached($host);

        $pins = [];
        foreach ($ips as $ip) {
            if (! $this->isSafeIp($ip)) {
                throw new \RuntimeException('SSRF Protection: Blocked unsafe IP: '.$ip);
            }
            $pins[] = $host.':'.$port.':'.$ip;
        }

        return $pins;
    }

    public function buildConnectTimeMiddleware(): callable
    {
        $service = $this;

        return function (callable $handler) use ($service) {
            return function (RequestInterface $request, array $options) use ($handler, $service) {
                $url = (string) $request->getUri();
                $pins = $service->resolvePinsForUrl($url);

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

    public function buildRedirectMiddleware(): callable
    {
        $service = $this;

        return function (callable $handler) use ($service) {
            return function (RequestInterface $request, array $options) use ($handler, $service) {
                if (! empty($options['allow_redirects'])) {
                    $options['allow_redirects']['on_redirect'] = function (
                        RequestInterface $req,
                        ResponseInterface $res,
                        UriInterface $uri
                    ) use ($service) {
                        $service->resetHostIpCache();
                        $redirectUrl = (string) $uri;
                        if (! $service->isSafeUrl($redirectUrl)) {
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
    private function resolveHostIpsCached(string $host): array
    {
        if (! isset($this->hostIpCache[$host])) {
            $this->hostIpCache[$host] = $this->resolveHostIps($host);
        }

        return $this->hostIpCache[$host];
    }

    /**
     * @return list<string>
     */
    private function resolveHostIps(string $host): array
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

    private static ?self $fallback = null;

    public static function resolve(): self
    {
        if (function_exists('app')) {
            try {
                return app(self::class);
            } catch (\Throwable) {
            }
        }

        return self::$fallback ??= new self;
    }

    public static function clearHostIpCache(): void
    {
        self::resolve()->resetHostIpCache();
    }
}
