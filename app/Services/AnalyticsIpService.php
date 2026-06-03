<?php

namespace App\Services;

class AnalyticsIpService
{
    /**
     * Reduce IP to a coarse network prefix before hashing (privacy).
     */
    public static function anonymizeForHash(string $ip): string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return preg_replace('/\.\d+$/', '.0', $ip) ?? $ip;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $packed = inet_pton($ip);
            if ($packed === false) {
                return $ip;
            }

            $masked = substr($packed, 0, 6).str_repeat("\0", 10);

            return inet_ntop($masked) ?: $ip;
        }

        return $ip;
    }
}
