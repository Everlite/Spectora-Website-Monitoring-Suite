<?php

namespace App\SpectoraEngine\Watchdog;

use App\Models\Domain;
use App\Services\SecurityService;
use Symfony\Component\DomCrawler\Crawler;

class SpectoraWatchdogEngine
{
    /**
     * Categorized high-confidence spam and malware patterns
     */
    protected array $spamKeywords = [
        'pharma' => ['viagra', 'cialis', 'levitra', 'pharmacy online', 'pills online', 'buy medication without prescription'],
        'gambling' => ['casino online', 'poker online', 'slot machine bonus', 'roulette live', 'crypto bet'],
        'adult' => ['xxx adult', 'sex video online', 'live cam girls', 'escort service'],
        'counterfeit' => ['replica watches', 'cheap rolex', 'fake designer bags', 'gucci replica outlet'],
        'crypto_scam' => ['guaranteed crypto returns', 'double your bitcoin in 24h', 'instant ether multiplier', 'deposit btc earn 500%'],
    ];

    /**
     * Suspicious URL shorteners & High-abuse TLDs
     */
    protected array $suspiciousShorteners = ['bit.ly', 'tinyurl.com', 'goo.gl', 'ow.ly', 'is.gd', 'buff.ly'];
    protected array $suspiciousTlds = ['.ru', '.cn', '.tk', '.ml', '.ga', '.cf', '.gq', '.top', '.work'];

    /**
     * Trusted CDN & script providers
     */
    protected array $trustedScriptDomains = [
        'cloudflare.com', 'cdnjs.cloudflare.com', 'jquery.com', 'jsdelivr.net', 'unpkg.com',
        'google.com', 'googleapis.com', 'gstatic.com', 'googletagmanager.com',
        'stripe.com', 'paypal.com', 'recaptcha.net', 'hcaptcha.com',
    ];

    /**
     * Scans the website HTML body for deep security threats, defacements, and malware.
     *
     * @param  Domain  $domain  Target domain model
     * @param  string|null  $url  URL to check (defaults to domain URL)
     * @param  string|null  $prefetchedBody  Prefetched HTML content to avoid redundant HTTP requests
     * @param  int|null  $prefetchedStatus  Prefetched HTTP status code
     * @param  array<string>  $allowlistedHosts  Custom hosts to ignore for this domain
     */
    public function scan(
        Domain $domain,
        ?string $url = null,
        ?string $prefetchedBody = null,
        ?int $prefetchedStatus = null,
        array $allowlistedHosts = []
    ): WatchdogResult {
        $url = $url ?? $domain->url;
        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $url = 'https://'.$url;
        }

        // SSRF Check
        if (! SecurityService::resolve()->isSafeUrl($url)) {
            return new WatchdogResult(
                status: 'error',
                issues: [[
                    'type' => 'security_blocked',
                    'severity' => 'critical',
                    'title' => 'SSRF-Schutz',
                    'description' => 'Request von Spectora blockiert (private oder gesperrte IP).',
                ]],
                summary: ['critical' => 1, 'warning' => 0, 'info' => 0]
            );
        }

        $issues = [];
        $summary = ['critical' => 0, 'warning' => 0, 'info' => 0];

        try {
            if ($prefetchedBody !== null) {
                $httpStatus = $prefetchedStatus ?? 200;
                if ($httpStatus >= 400 || $httpStatus === 0) {
                    return new WatchdogResult(
                        status: 'error',
                        issues: [[
                            'type' => 'unreachable',
                            'severity' => 'critical',
                            'title' => 'Seite nicht erreichbar',
                            'description' => "HTTP {$httpStatus}.",
                            'recommendation' => 'Erreichbarkeit und Firewall prüfen.',
                        ]],
                        summary: ['critical' => 1, 'warning' => 0, 'info' => 0]
                    );
                }
                $body = $prefetchedBody;
            } else {
                $response = SecurityService::resolve()->httpClient()
                    ->withUserAgent('SpectoraBot/2.0 (Watchdog Engine; +'.rtrim((string) config('app.url'), '/').')')
                    ->timeout(15)
                    ->get($url);

                if ($response->failed()) {
                    return new WatchdogResult(
                        status: 'error',
                        issues: [[
                            'type' => 'unreachable',
                            'severity' => 'critical',
                            'title' => 'Seite nicht erreichbar',
                            'description' => "HTTP {$response->status()}.",
                        ]],
                        summary: ['critical' => 1, 'warning' => 0, 'info' => 0]
                    );
                }

                $body = $response->body();
            }

            $bodyLower = strtolower($body);
            $crawler = new Crawler($body);

            // 1. Check for Obfuscated / Malicious Scripts (Hex, eval packing, cryptominers)
            $scriptThreats = $this->checkObfuscatedScripts($body, $crawler, $allowlistedHosts);
            foreach ($scriptThreats as $issue) {
                $issues[] = $issue;
                $summary[$issue['severity']]++;
            }

            // 2. Check for Title Hijacking (SEO Spam Hack)
            $titleIssue = $this->checkTitleHijacking($crawler);
            if ($titleIssue) {
                $issues[] = $titleIssue;
                $summary[$titleIssue['severity']]++;
            }

            // 3. Check for Categorized Spam Keywords in Body
            $spamIssues = $this->checkSpamKeywords($bodyLower);
            foreach ($spamIssues as $issue) {
                $issues[] = $issue;
                $summary[$issue['severity']]++;
            }

            // 4. Check for Hidden Blackhat SEO Content (display:none with dense text)
            $hiddenIssue = $this->checkHiddenContent($body);
            if ($hiddenIssue) {
                $issues[] = $hiddenIssue;
                $summary[$hiddenIssue['severity']]++;
            }

            // 5. Check for Zero-Pixel / Suspicious Iframes
            $iframeIssues = $this->checkIframes($crawler);
            foreach ($iframeIssues as $issue) {
                $issues[] = $issue;
                $summary[$issue['severity']]++;
            }

            // 6. Check for Meta-Refresh Redirects (Cloaking)
            $metaRedirectIssue = $this->checkMetaRefresh($crawler);
            if ($metaRedirectIssue) {
                $issues[] = $metaRedirectIssue;
                $summary[$metaRedirectIssue['severity']]++;
            }

            // 7. Check for Suspicious Links
            $linkIssues = $this->checkSuspiciousLinks($crawler, $url, $allowlistedHosts);
            foreach ($linkIssues as $issue) {
                $issues[] = $issue;
                $summary[$issue['severity']]++;
            }

        } catch (\Throwable $e) {
            return new WatchdogResult(
                status: 'warning',
                issues: [[
                    'type' => 'scan_interrupted',
                    'severity' => 'warning',
                    'title' => 'Watchdog unterbrochen',
                    'description' => $e->getMessage(),
                ]],
                summary: ['critical' => 0, 'warning' => 1, 'info' => 0]
            );
        }

        $overallStatus = 'safe';
        if ($summary['critical'] > 0) {
            $overallStatus = 'danger';
        } elseif ($summary['warning'] > 0) {
            $overallStatus = 'warning';
        }

        return new WatchdogResult(
            status: $overallStatus,
            issues: $issues,
            summary: $summary
        );
    }

    private function checkObfuscatedScripts(string $body, Crawler $crawler, array $allowlistedHosts): array
    {
        $issues = [];

        // Check for eval(String.fromCharCode(...)) or document.write(unescape(...))
        if (preg_match('/(eval\s*\(\s*String\.fromCharCode|document\.write\s*\(\s*unescape|\bcoinhive\b|\bwebminerpool\b)/i', $body)) {
            $issues[] = [
                'type' => 'malicious_eval',
                'severity' => 'critical',
                'title' => 'Verschleiertes JavaScript',
                'description' => 'eval/fromCharCode, unescape oder Miner-Signatur im Markup.',
                'recommendation' => 'Script-Tags und zuletzt geänderte Plugins/Themes prüfen.',
            ];
        }

        // Check external scripts
        foreach ($crawler->filter('script[src]') as $element) {
            $src = (new Crawler($element))->attr('src');
            if (! $src) {
                continue;
            }

            $host = strtolower((string) parse_url($src, PHP_URL_HOST));
            if (! $host) {
                continue;
            }

            // Check against trusted domains and allowlist
            $isTrusted = in_array($host, $allowlistedHosts, true);
            if (! $isTrusted) {
                foreach ($this->trustedScriptDomains as $trusted) {
                    if ($host === $trusted || str_ends_with($host, '.'.$trusted)) {
                        $isTrusted = true;
                        break;
                    }
                }
            }

            if (! $isTrusted) {
                foreach ($this->suspiciousTlds as $tld) {
                    if (str_ends_with($host, $tld)) {
                        $issues[] = [
                            'type' => 'suspicious_script_host',
                            'severity' => 'critical',
                            'title' => 'Script von verdächtiger TLD',
                            'description' => "Externes Script von {$host}.",
                            'recommendation' => 'Prüfen, ob dieses Script bewusst eingebunden ist.',
                        ];
                        break;
                    }
                }
            }
        }

        return $issues;
    }

    private function checkTitleHijacking(Crawler $crawler): ?array
    {
        $titleNode = $crawler->filter('title');
        if ($titleNode->count() === 0) {
            return null;
        }

        $title = trim($titleNode->text());

        // Check for CJK character injection in non-Asian titles (classic SEO spam hack)
        if (preg_match('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $title)) {
            return [
                'type' => 'title_hijacked',
                'severity' => 'critical',
                'title' => 'Titel-Hijack (CJK-Spam)',
                'description' => 'Fremde Schriftzeichen im Title: „'.mb_substr($title, 0, 45).'…“',
                'recommendation' => 'Datenbank, .htaccess und Admin-Zugang prüfen.',
            ];
        }

        return null;
    }

    private function checkSpamKeywords(string $bodyLower): array
    {
        $issues = [];

        foreach ($this->spamKeywords as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($bodyLower, $keyword)) {
                    $pos = strpos($bodyLower, $keyword);
                    $start = max(0, $pos - 30);
                    $snippet = substr($bodyLower, $start, 90);

                    $titles = [
                        'pharma' => 'Pharma-Spam',
                        'gambling' => 'Glücksspiel-Keywords',
                        'adult' => 'Adult-Content',
                        'counterfeit' => 'Fälschungs-Spam',
                        'crypto_scam' => 'Crypto-Scam-Muster',
                    ];

                    $issues[] = [
                        'type' => 'spam_keyword_'.$category,
                        'severity' => 'critical',
                        'title' => $titles[$category] ?? 'Spam-Keyword',
                        'description' => "Unerwartetes Keyword: „{$keyword}“",
                        'context' => '..."'.trim(preg_replace('/\s+/', ' ', $snippet)).'"...',
                        'recommendation' => 'Code und Datenbank nach diesem String durchsuchen.',
                    ];
                    break; // One match per category is enough to alert
                }
            }
        }

        return $issues;
    }

    private function checkHiddenContent(string $body): ?array
    {
        if (preg_match('/<[^>]+style=["\'][^"\']*(?:display:\s*none|visibility:\s*hidden|font-size:\s*0px|position:\s*absolute;\s*left:\s*-\d+px)[^"\']*["\'][^>]*>([^<]{30,})/i', $body, $matches)) {
            $hiddenText = trim(strip_tags($matches[1]));
            if (mb_strlen($hiddenText) > 25) {
                return [
                    'type' => 'hidden_text_cloaking',
                    'severity' => 'critical',
                    'title' => 'Versteckter SEO-Text',
                    'description' => 'Versteckter Block: „'.mb_substr($hiddenText, 0, 60).'…“',
                    'recommendation' => 'Template prüfen und verstecktes Keyword-Stuffing entfernen.',
                ];
            }
        }

        return null;
    }

    private function checkIframes(Crawler $crawler): array
    {
        $issues = [];
        $safeHosts = ['youtube.com', 'youtube-nocookie.com', 'vimeo.com', 'player.vimeo.com', 'google.com', 'maps.google.com'];

        foreach ($crawler->filter('iframe') as $iframe) {
            $node = new Crawler($iframe);
            $src = $node->attr('src');
            $style = strtolower((string) $node->attr('style'));
            $width = $node->attr('width');
            $height = $node->attr('height');

            // Zero-pixel iframe detection
            if (str_contains($style, 'display:none') || str_contains($style, 'visibility:hidden') || ($width === '0' && $height === '0') || ($width === '1' && $height === '1')) {
                $issues[] = [
                    'type' => 'hidden_iframe',
                    'severity' => 'critical',
                    'title' => 'Verstecktes Iframe',
                    'description' => 'Iframe unsichtbar oder 1px (Ziel: '.mb_substr($src ?? 'unbekannt', 0, 50).')',
                    'recommendation' => 'Unbefugtes Iframe entfernen.',
                ];
                continue;
            }

            if (! $src) {
                continue;
            }

            $srcHost = strtolower((string) parse_url($src, PHP_URL_HOST));
            $isSafe = false;
            foreach ($safeHosts as $safe) {
                if ($srcHost === $safe || str_ends_with($srcHost, '.'.$safe)) {
                    $isSafe = true;
                    break;
                }
            }

            if (! $isSafe && ! empty($srcHost)) {
                $issues[] = [
                    'type' => 'unknown_iframe',
                    'severity' => 'warning',
                    'title' => 'Fremdes Iframe',
                    'description' => "Lädt {$srcHost}.",
                    'recommendation' => 'Prüfen, ob die Quelle gewollt und DSGVO-konform ist.',
                ];
            }
        }

        return $issues;
    }

    private function checkMetaRefresh(Crawler $crawler): ?array
    {
        $meta = $crawler->filter('meta[http-equiv="refresh"]');
        if ($meta->count() > 0) {
            $content = $meta->attr('content') ?? '';
            return [
                'type' => 'meta_refresh_redirect',
                'severity' => 'warning',
                'title' => 'Meta-Refresh-Weiterleitung',
                'description' => 'Meta-Refresh: '.mb_substr($content, 0, 60),
                'recommendation' => 'Stattdessen HTTP 301 auf dem Server nutzen.',
            ];
        }

        return null;
    }

    private function checkSuspiciousLinks(Crawler $crawler, string $ownUrl, array $allowlistedHosts): array
    {
        $issues = [];
        $ownHost = parse_url($ownUrl, PHP_URL_HOST);

        foreach ($crawler->filter('a[href]') as $a) {
            $node = new Crawler($a);
            $href = $node->attr('href');
            if (! $href || str_starts_with($href, '#') || str_starts_with($href, '/') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) {
                continue;
            }

            $linkHost = strtolower((string) parse_url($href, PHP_URL_HOST));
            if (! $linkHost || $linkHost === $ownHost || in_array($linkHost, $allowlistedHosts, true)) {
                continue;
            }

            // Check Shorteners
            foreach ($this->suspiciousShorteners as $shortener) {
                if ($linkHost === $shortener || str_ends_with($linkHost, '.'.$shortener)) {
                    $issues[] = [
                        'type' => 'url_shortener_link',
                        'severity' => 'warning',
                        'title' => 'Shortener-Link',
                        'description' => "Ziel hinter Shortener: {$linkHost}.",
                        'recommendation' => 'Direkte URLs statt Shortener in der Navigation.',
                    ];
                    break 2;
                }
            }

            // Check High-Abuse TLDs
            foreach ($this->suspiciousTlds as $tld) {
                if (str_ends_with($linkHost, $tld)) {
                    $issues[] = [
                        'type' => 'suspicious_tld_link',
                        'severity' => 'warning',
                        'title' => 'Link zu verdächtiger TLD',
                        'description' => "Externes Ziel {$linkHost}.",
                        'recommendation' => 'Prüfen, ob der Link gewollt ist.',
                    ];
                    break 2;
                }
            }
        }

        return $issues;
    }
}
