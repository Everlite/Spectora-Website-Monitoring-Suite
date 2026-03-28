<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RobotsTxtService
{
    /**
     * Check if a URL is allowed by robots.txt
     */
    public function isAllowed(string $url, string $userAgent = 'SpectoraBot'): bool
    {
        $parsedUrl = parse_url($url);
        $baseUrl = ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? '');
        $robotsUrl = $baseUrl . '/robots.txt';

        $content = Cache::remember("robots_txt_" . md5($baseUrl), 3600, function () use ($robotsUrl) {
            try {
                $response = Http::timeout(5)->get($robotsUrl);
                return $response->successful() ? $response->body() : '';
            } catch (\Exception $e) {
                return '';
            }
        });

        if (empty($content)) {
            return true; // No robots.txt, assume allowed
        }

        return $this->parseAndCheck($content, $parsedUrl['path'] ?? '/', $userAgent);
    }

    /**
     * Minimal robots.txt parser
     */
    private function parseAndCheck(string $content, string $path, string $userAgent): bool
    {
        $lines = explode("\n", $content);
        $currentUA = null;
        $rules = [];

        foreach ($lines as $line) {
            $line = trim(preg_replace('/#.*$/', '', $line));
            if (empty($line)) continue;

            if (preg_match('/^User-agent:\s*(.*)$/i', $line, $matches)) {
                $currentUA = trim($matches[1]);
                if (!isset($rules[$currentUA])) $rules[$currentUA] = [];
            } elseif ($currentUA && preg_match('/^(Allow|Disallow):\s*(.*)$/i', $line, $matches)) {
                $type = strtolower($matches[1]);
                $pattern = trim($matches[2]);
                if ($pattern) {
                    $rules[$currentUA][] = ['type' => $type, 'pattern' => $pattern];
                }
            }
        }

        // 1. Check specific User-Agent
        if (isset($rules[$userAgent])) {
            return $this->checkRules($rules[$userAgent], $path);
        }

        // 2. Check wildcard User-Agent
        if (isset($rules['*'])) {
            return $this->checkRules($rules['*'], $path);
        }

        return true;
    }

    private function checkRules(array $rules, string $path): bool
    {
        // Simple prefix matching for now, as Spectora is "Smart" but not necessarily a full crawler engine
        // Standard robots.txt rules specify that the longest matching rule wins, or Disallow wins in case of tie.
        
        $applicableRules = [];
        foreach ($rules as $rule) {
            $regex = '#' . preg_quote($rule['pattern'], '#') . '#';
            // Convert * to .*
            $regex = str_replace('\*', '.*', $regex);
            
            if (preg_match($regex, $path) || str_starts_with($path, $rule['pattern'])) {
                $applicableRules[] = $rule;
            }
        }

        if (empty($applicableRules)) return true;

        // Sort by length of pattern descending (longest match)
        usort($applicableRules, fn($a, $b) => strlen($b['pattern']) <=> strlen($a['pattern']));

        return $applicableRules[0]['type'] === 'allow';
    }
}
