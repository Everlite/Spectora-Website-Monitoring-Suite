<?php

namespace App\Support;

class HostHelper
{
    public static function normalize(?string $host): ?string
    {
        if ($host === null || $host === '') {
            return null;
        }

        $host = strtolower($host);

        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        return $host;
    }

    public static function matches(?string $a, ?string $b): bool
    {
        $normalizedA = self::normalize($a);
        $normalizedB = self::normalize($b);

        return $normalizedA !== null && $normalizedA === $normalizedB;
    }

    public static function fromUrl(string $url): ?string
    {
        return self::normalize(parse_url($url, PHP_URL_HOST) ?: null);
    }
}
