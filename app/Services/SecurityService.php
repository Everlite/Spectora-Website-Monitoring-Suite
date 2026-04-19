<?php

namespace App\Services;

class SecurityService
{
    /**
     * Checks if a URL points to a private/reserved IP address (SSRF protection).
     *
     * @param string $url
     * @return bool True if the URL is safe, false if it points to a private/internal IP.
     */
    public static function isSafeUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        
        if (!$host) {
            return false;
        }

        // Prevent immediate loopback names
        if ($host === 'localhost' || $host === 'loopback') {
            return false;
        }

        // 1. If it's already an IP, check it directly
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return self::isSafeIp($host);
        }

        // 2. Resolve ALL IPs (IPv4 and IPv6)
        $records = [];
        try {
            $aRecords = dns_get_record($host, DNS_A) ?: [];
            $aaaaRecords = dns_get_record($host, DNS_AAAA) ?: [];
            $records = array_merge($aRecords, $aaaaRecords);
        } catch (\Exception $e) {
            // Fallback to basic resolution if dns_get_record fails
            $ip = gethostbyname($host);
            if ($ip !== $host) {
                return self::isSafeIp($ip);
            }
            return false;
        }

        if (empty($records)) {
            return false;
        }

        foreach ($records as $record) {
            $ip = $record['ip'] ?? ($record['ipv6'] ?? null);
            if ($ip && !self::isSafeIp($ip)) {
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
        // Check for private/reserved ranges
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }

        // Explicit loopback checks
        if ($ip === '127.0.0.1' || $ip === '::1' || str_starts_with($ip, '127.')) {
            return false;
        }

        return true;
    }

    /**
     * Guzzle middleware to prevent SSRF in redirects.
     */
    public static function redirectMiddleware()
    {
        return function (callable $handler) {
            return function (\Psr\Http\Message\RequestInterface $request, array $options) use ($handler) {
                // If this is a redirect, validate the target
                if (!empty($options['allow_redirects'])) {
                    $options['allow_redirects']['on_redirect'] = function (
                        \Psr\Http\Message\RequestInterface $req,
                        \Psr\Http\Message\ResponseInterface $res,
                        \Psr\Http\Message\UriInterface $uri
                    ) {
                        $url = (string) $uri;
                        if (!self::isSafeUrl($url)) {
                            throw new \Exception("SSRF Protection: Blocked redirect to unsafe URL: " . $url);
                        }
                    };
                }
                return $handler($request, $options);
            };
        };
    }
}
