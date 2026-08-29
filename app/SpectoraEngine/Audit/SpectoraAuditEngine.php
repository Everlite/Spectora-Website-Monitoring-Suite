<?php

namespace App\SpectoraEngine\Audit;

use App\Models\Domain;
use App\Services\SecurityService;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class SpectoraAuditEngine
{
    /**
     * Executes a comprehensive multi-factor audit on the target domain.
     */
    public function audit(Domain $domain): AuditResult
    {
        $url = $domain->url;
        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $url = 'https://'.$url;
        }

        // SSRF Check
        if (! SecurityService::resolve()->isSafeUrl($url)) {
            return new AuditResult(
                score: 0,
                grade: 'F',
                metrics: ['ttfb_ms' => 0, 'size_bytes' => 0, 'status_code' => 0],
                findings: [[
                    'category' => 'security',
                    'label' => 'SSRF Protection Block',
                    'status' => 'error',
                    'message' => 'The target URL resolved to a prohibited or private IP address.',
                    'impact' => -100,
                ]]
            );
        }

        $ttfb = 0;
        $totalTime = 0;
        $score = 100;
        $findings = [];
        $statusCode = 0;
        $sizeBytes = 0;
        $headers = [];

        try {
            $response = SecurityService::resolve()->httpClient()
                ->withUserAgent('SpectoraBot/2.0 (Spectora Engine Audit; +'.rtrim((string) config('app.url'), '/').')')
                ->withOptions([
                    'on_stats' => function (TransferStats $stats) use (&$ttfb, &$totalTime) {
                        if (isset($stats->getHandlerStats()['starttransfer_time'])) {
                            $ttfb = $stats->getHandlerStats()['starttransfer_time'];
                        }
                        $totalTime = $stats->getTransferTime();
                    },
                ])
                ->timeout(15)
                ->get($url);

            $statusCode = $response->status();
            $body = $response->body();
            $sizeBytes = strlen($body);
            $headers = array_change_key_case($response->headers(), CASE_LOWER);

            if ($response->failed()) {
                return new AuditResult(
                    score: 10,
                    grade: 'F',
                    metrics: ['ttfb_ms' => round($ttfb * 1000), 'size_bytes' => $sizeBytes, 'status_code' => $statusCode],
                    findings: [[
                        'category' => 'performance',
                        'label' => 'HTTP Status',
                        'status' => 'error',
                        'message' => "Server responded with HTTP error status {$statusCode}.",
                        'impact' => -90,
                    ]]
                );
            }

            $crawler = new Crawler($body);

            // ══════════════════════════════════════════════════════
            // FACTOR 1: Server Performance & TTFB (30 pts weight)
            // ══════════════════════════════════════════════════════
            $ttfbMs = round($ttfb * 1000);
            if ($ttfbMs > 1200) {
                $score -= 20;
                $findings[] = [
                    'category' => 'performance',
                    'label' => 'Server Response Time (TTFB)',
                    'status' => 'error',
                    'message' => "TTFB is critically slow ({$ttfbMs}ms). Target: < 400ms.",
                    'recommendation' => 'Check server caching, database bottlenecks, or hosting specs.',
                ];
            } elseif ($ttfbMs > 500) {
                $score -= 10;
                $findings[] = [
                    'category' => 'performance',
                    'label' => 'Server Response Time (TTFB)',
                    'status' => 'warning',
                    'message' => "TTFB is moderate ({$ttfbMs}ms). Target: < 400ms.",
                    'recommendation' => 'Enable page or object caching (Redis/OPcache/Varnish).',
                ];
            } else {
                $findings[] = [
                    'category' => 'performance',
                    'label' => 'Server Response Time (TTFB)',
                    'status' => 'success',
                    'message' => "Blazing fast server response time ({$ttfbMs}ms).",
                ];
            }

            // HTML Payload Size
            if ($sizeBytes > 2 * 1024 * 1024) {
                $score -= 10;
                $findings[] = [
                    'category' => 'performance',
                    'label' => 'HTML Payload Size',
                    'status' => 'error',
                    'message' => 'Initial HTML document is very large ('.round($sizeBytes / 1024 / 1024, 2).' MB).',
                    'recommendation' => 'Reduce inline assets, scripts, and heavy DOM trees.',
                ];
            } elseif ($sizeBytes > 500 * 1024) {
                $score -= 5;
                $findings[] = [
                    'category' => 'performance',
                    'label' => 'HTML Payload Size',
                    'status' => 'warning',
                    'message' => 'HTML size is larger than average ('.round($sizeBytes / 1024).' KB).',
                ];
            } else {
                $findings[] = [
                    'category' => 'performance',
                    'label' => 'HTML Payload Size',
                    'status' => 'success',
                    'message' => 'HTML weight is lightweight ('.round($sizeBytes / 1024).' KB).',
                ];
            }

            // ══════════════════════════════════════════════════════
            // FACTOR 2: Semantic SEO & Head Structure (30 pts weight)
            // ══════════════════════════════════════════════════════
            // 1. Title Tag
            $titleNodes = $crawler->filter('title');
            $title = $titleNodes->count() > 0 ? trim($titleNodes->text()) : '';
            if (empty($title)) {
                $score -= 15;
                $findings[] = [
                    'category' => 'seo',
                    'label' => 'Page Title (<title>)',
                    'status' => 'error',
                    'message' => 'Missing or empty <title> tag.',
                    'recommendation' => 'Add an expressive page title (50–60 characters).',
                ];
            } elseif (mb_strlen($title) < 15 || mb_strlen($title) > 75) {
                $score -= 5;
                $findings[] = [
                    'category' => 'seo',
                    'label' => 'Page Title (<title>)',
                    'status' => 'warning',
                    'message' => "Title length (".mb_strlen($title)." chars) is outside optimal range (40–60 chars).",
                    'recommendation' => 'Refine title tag to fit search engine snippet displays.',
                ];
            } else {
                $findings[] = [
                    'category' => 'seo',
                    'label' => 'Page Title (<title>)',
                    'status' => 'success',
                    'message' => "Optimal title defined: \"".mb_substr($title, 0, 50)."...\".",
                ];
            }

            // 2. Meta Description
            $metaDescNodes = $crawler->filter('meta[name="description"], meta[property="og:description"]');
            $metaDesc = $metaDescNodes->count() > 0 ? trim($metaDescNodes->attr('content') ?? '') : '';
            if (empty($metaDesc)) {
                $score -= 10;
                $findings[] = [
                    'category' => 'seo',
                    'label' => 'Meta Description',
                    'status' => 'warning',
                    'message' => 'No meta description found.',
                    'recommendation' => 'Provide a compelling description (120–160 characters).',
                ];
            } else {
                $findings[] = [
                    'category' => 'seo',
                    'label' => 'Meta Description',
                    'status' => 'success',
                    'message' => 'Meta description is present.',
                ];
            }

            // 3. H1 Hierarchy
            $h1Count = $crawler->filter('h1')->count();
            if ($h1Count === 0) {
                $score -= 10;
                $findings[] = [
                    'category' => 'seo',
                    'label' => 'H1 Heading',
                    'status' => 'error',
                    'message' => 'No <h1> heading found on the page.',
                    'recommendation' => 'Add exactly one primary <h1> heading to represent the page theme.',
                ];
            } elseif ($h1Count > 1) {
                $score -= 3;
                $findings[] = [
                    'category' => 'seo',
                    'label' => 'H1 Heading Hierarchy',
                    'status' => 'warning',
                    'message' => "Found multiple ({$h1Count}) <h1> headings. Single H1 per page is best practice.",
                ];
            } else {
                $findings[] = [
                    'category' => 'seo',
                    'label' => 'H1 Heading',
                    'status' => 'success',
                    'message' => 'Clean single <h1> structure detected.',
                ];
            }

            // 4. Viewport / Mobile Friendliness
            $viewportCount = $crawler->filter('meta[name="viewport"]')->count();
            if ($viewportCount === 0) {
                $score -= 10;
                $findings[] = [
                    'category' => 'seo',
                    'label' => 'Mobile Viewport Meta',
                    'status' => 'error',
                    'message' => 'Missing <meta name="viewport"> tag.',
                    'recommendation' => 'Add <meta name="viewport" content="width=device-width, initial-scale=1">.',
                ];
            }

            // ══════════════════════════════════════════════════════
            // FACTOR 3: Accessibility & Media (20 pts weight)
            // ══════════════════════════════════════════════════════
            $images = $crawler->filter('img');
            $totalImages = $images->count();
            $missingAlt = 0;
            if ($totalImages > 0) {
                foreach ($images as $img) {
                    $node = new Crawler($img);
                    $alt = $node->attr('alt');
                    if ($alt === null || trim($alt) === '') {
                        $missingAlt++;
                    }
                }
            }

            if ($missingAlt > 0) {
                $penalty = min(15, $missingAlt * 3);
                $score -= $penalty;
                $findings[] = [
                    'category' => 'accessibility',
                    'label' => 'Image Alt Attributes',
                    'status' => $missingAlt > 3 ? 'error' : 'warning',
                    'message' => "{$missingAlt} of {$totalImages} images missing descriptive alt tags.",
                    'recommendation' => 'Add descriptive alt text to all informational images for screen readers and SEO.',
                ];
            } elseif ($totalImages > 0) {
                $findings[] = [
                    'category' => 'accessibility',
                    'label' => 'Image Alt Attributes',
                    'status' => 'success',
                    'message' => "All {$totalImages} images have valid alt attributes.",
                ];
            }

            // ══════════════════════════════════════════════════════
            // FACTOR 4: Security & Headers (20 pts weight)
            // ══════════════════════════════════════════════════════
            $isHttps = str_starts_with($url, 'https://');
            if (! $isHttps) {
                $score -= 20;
                $findings[] = [
                    'category' => 'security',
                    'label' => 'HTTPS Encryption',
                    'status' => 'error',
                    'message' => 'Site does not enforce HTTPS.',
                    'recommendation' => 'Install a valid SSL certificate and enforce HTTPS redirects.',
                ];
            } else {
                $findings[] = [
                    'category' => 'security',
                    'label' => 'HTTPS Encryption',
                    'status' => 'success',
                    'message' => 'Secure HTTPS connection active.',
                ];
            }

            // Check Modern Security Headers (HSTS, CSP, X-Frame-Options, X-Content-Type-Options)
            $hasHsts = isset($headers['strict-transport-security']);
            $hasXFrame = isset($headers['x-frame-options']);
            $hasXContent = isset($headers['x-content-type-options']);

            if (! $hasHsts && $isHttps) {
                $score -= 5;
                $findings[] = [
                    'category' => 'security',
                    'label' => 'HSTS Security Header',
                    'status' => 'warning',
                    'message' => 'Strict-Transport-Security (HSTS) header is missing.',
                    'recommendation' => 'Add Strict-Transport-Security header in web server configuration.',
                ];
            } elseif ($hasHsts) {
                $findings[] = [
                    'category' => 'security',
                    'label' => 'HSTS Security Header',
                    'status' => 'success',
                    'message' => 'HSTS header configured.',
                ];
            }

            if (! $hasXFrame && ! isset($headers['content-security-policy'])) {
                $findings[] = [
                    'category' => 'security',
                    'label' => 'Clickjacking Protection',
                    'status' => 'warning',
                    'message' => 'X-Frame-Options or Content-Security-Policy header recommended.',
                ];
            }

        } catch (\Throwable $e) {
            Log::error("SpectoraAuditEngine exception for {$url}: ".$e->getMessage());
            $score = 0;
            $findings[] = [
                'category' => 'performance',
                'label' => 'Audit Execution Failed',
                'status' => 'error',
                'message' => 'Audit encountered an unexpected exception: '.$e->getMessage(),
            ];
        }

        $finalScore = max(0, min(100, $score));
        $grade = self::calculateGrade($finalScore);

        return new AuditResult(
            score: $finalScore,
            grade: $grade,
            metrics: [
                'ttfb_ms' => round($ttfb * 1000),
                'total_time_ms' => round($totalTime * 1000),
                'size_bytes' => $sizeBytes,
                'status_code' => $statusCode,
            ],
            findings: $findings
        );
    }

    public static function calculateGrade(int $score): string
    {
        return match (true) {
            $score >= 95 => 'A+',
            $score >= 85 => 'A',
            $score >= 75 => 'B',
            $score >= 60 => 'C',
            $score >= 40 => 'D',
            default => 'F',
        };
    }
}
