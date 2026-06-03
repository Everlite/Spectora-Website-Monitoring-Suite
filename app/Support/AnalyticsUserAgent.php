<?php

namespace App\Support;

class AnalyticsUserAgent
{
    public static function browser(string $ua): string
    {
        $ua = strtolower($ua);

        if (str_contains($ua, 'edg/')) {
            return 'Edge';
        }
        if (str_contains($ua, 'opr/') || str_contains($ua, 'opera')) {
            return 'Opera';
        }
        if (str_contains($ua, 'firefox')) {
            return 'Firefox';
        }
        if (str_contains($ua, 'chrome')) {
            return 'Chrome';
        }
        if (str_contains($ua, 'safari')) {
            return 'Safari';
        }

        return 'Other';
    }

    public static function os(string $ua): string
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
