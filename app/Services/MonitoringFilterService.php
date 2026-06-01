<?php

namespace App\Services;

use App\Models\Domain;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class MonitoringFilterService
{
    public function __construct(
        protected RobotsTxtService $robotsTxt,
    ) {}

    /**
     * Determines if a URL should be checked based on domain settings.
     */
    public function shouldCheck(Domain $domain, string $url): array
    {
        $url = trim($url);
        if (empty($url)) {
            return ['should_check' => false, 'reason' => 'Empty URL'];
        }

        // 1. URL Pattern Exclusions
        if ($domain->exclude_patterns) {
            $patterns = array_filter(explode("\n", str_replace("\r", "", $domain->exclude_patterns)));
            foreach ($patterns as $pattern) {
                if ($this->matchPattern(trim($pattern), $url)) {
                    $reason = 'URL matched exclude pattern: ' . $pattern;
                    Log::info("MonitoringFilter: Skipping {$url} - {$reason}");
                    return ['should_check' => false, 'reason' => $reason];
                }
            }
        }

        // 2. Robots.txt Respect
        if ($domain->respect_robots_txt) {
            if (!$this->robotsTxt->isAllowed($url)) {
                $reason = 'Disallowed by robots.txt';
                Log::info("MonitoringFilter: Skipping {$url} - {$reason}");
                return ['should_check' => false, 'reason' => $reason];
            }
        }

        // 3. Noindex and Auth logic (can only be checked AFTER fetch or via HEAD)
        // For the purpose of "BEFORE checking a page", we've done all we can.
        // The check job will handle the after-fetch checks (noindex, auth).

        return ['should_check' => true, 'reason' => 'Allowed'];
    }

    /**
     * Determines if a fetched response should be ignored (noindex, auth).
     */
    public function shouldIgnoreResponse(Domain $domain, $response): array
    {
        // 1. Respect Noindex (Meta & Header)
        if ($domain->respect_noindex) {
            // Header X-Robots-Tag
            $xRobots = $response->header('X-Robots-Tag');
            if ($xRobots && stripos($xRobots, 'noindex') !== false) {
                return ['ignore' => true, 'reason' => 'noindex header found'];
            }

            if ($this->bodyHasNoindexMeta($response->body())) {
                return ['ignore' => true, 'reason' => 'noindex meta tag found'];
            }
        }

        // 2. Require Authentication (Login Page Detection)
        // Spectora is a "Public-Only" monitoring tool, so we treat login pages as non-public areas to skip.
        if ($domain->only_check_public_pages) {
            $body = $response->body();
            // Simple indicators of a login form
            if (preg_match('/<input[^>]*type=["\']password["\'][^>]*>/i', $body)) {
                return ['ignore' => true, 'reason' => 'Authentication required (login form detected)'];
            }
            if ($response->status() === 401 || $response->status() === 403) {
                return ['ignore' => true, 'reason' => 'Access denied (HTTP ' . $response->status() . ')'];
            }
        }

        return ['ignore' => false, 'reason' => ''];
    }

    private function bodyHasNoindexMeta(string $html): bool
    {
        if ($html === '') {
            return false;
        }

        try {
            $crawler = new Crawler($html);

            foreach ($crawler->filter('meta') as $node) {
                $name = strtolower($node->attr('name') ?? '');
                if ($name !== 'robots') {
                    continue;
                }

                $content = strtolower($node->attr('content') ?? '');
                if (preg_match('/\bnoindex\b/', $content)) {
                    return true;
                }
            }
        } catch (\Exception $e) {
            Log::debug('MonitoringFilter: DOM parse failed for noindex check: '.$e->getMessage());
        }

        return false;
    }

    /**
     * Matches a pattern against a URL (Glob/Shell-style)
     */
    private function matchPattern(string $pattern, string $url): bool
    {
        // Remove host to match relative path or just use shell pattern
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        
        // Convert shell-style wildcard to regex
        // E.g. */downloads/* -> #/.*/downloads/.*#
        $regex = '#' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '#i';
        
        return (bool) preg_match($regex, $url) || (bool) preg_match($regex, $path);
    }
}
