<?php

namespace App\SpectoraEngine\Pulse;

use App\Services\AnalyticsIpService;

class PulseAnonymizer
{
    /**
     * Generates a non-reversible, daily-rotated visitor hash compliant with GDPR.
     */
    public static function generateVisitorHash(?string $ip, ?string $userAgent, ?string $date = null): string
    {
        $date = $date ?? now()->format('Y-m-d');
        $dailyKey = hash_hmac('sha256', $date, (string) config('app.key'));
        
        $ipForHash = $ip ? AnalyticsIpService::anonymizeForHash($ip) : '';
        $cleanUa = $userAgent ?? 'Unknown';

        return hash_hmac('sha256', $ipForHash.'|'.$cleanUa, $dailyKey);
    }
}
