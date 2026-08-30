<?php

namespace App\SpectoraEngine\Audit;

use App\Models\Domain;
use App\Services\SecurityService;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class SpectoraAuditEngine
{
    public function audit(Domain $domain): AuditResult
    {
        $url = $domain->url;
        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $url = 'https://'.$url;
        }

        if (! SecurityService::resolve()->isSafeUrl($url)) {
            return new AuditResult(
                score: 0,
                grade: 'F',
                metrics: ['ttfb_ms' => 0, 'size_bytes' => 0, 'status_code' => 0],
                findings: [$this->finding('security', 'SSRF-Schutz', 'error', 'Die Ziel-URL zeigt auf eine private oder gesperrte IP. Spectora hat den Request nicht ausgeführt.')]
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
                    findings: [$this->finding('performance', 'HTTP-Status', 'error', "Server antwortet mit HTTP {$statusCode}.", 'Prüfen, ob die Seite erreichbar ist und SpectoraBot nicht blockiert wird.')]
                );
            }

            $crawler = new Crawler($body);

            $ttfbMs = (int) round($ttfb * 1000);
            if ($ttfbMs > 1200) {
                $score -= 20;
                $findings[] = $this->finding('performance', 'Antwortzeit (TTFB)', 'error', "{$ttfbMs} ms — über dem Grenzwert von 1200 ms.", 'Caching, Datenbank und Hosting prüfen. Ziel: unter 400 ms.');
            } elseif ($ttfbMs > 500) {
                $score -= 10;
                $findings[] = $this->finding('performance', 'Antwortzeit (TTFB)', 'warning', "{$ttfbMs} ms — langsamer als das Ziel von 400 ms.", 'Seiten- oder Object-Cache (OPcache, Redis) aktivieren.');
            } else {
                $findings[] = $this->finding('performance', 'Antwortzeit (TTFB)', 'success', "{$ttfbMs} ms (Ziel: unter 400 ms).");
            }

            $kb = (int) round($sizeBytes / 1024);
            if ($sizeBytes > 2 * 1024 * 1024) {
                $score -= 10;
                $findings[] = $this->finding('performance', 'HTML-Größe', 'error', round($sizeBytes / 1024 / 1024, 2).' MB — das erste Dokument ist zu groß.', 'Inline-Assets und überladenes Markup reduzieren.');
            } elseif ($sizeBytes > 500 * 1024) {
                $score -= 5;
                $findings[] = $this->finding('performance', 'HTML-Größe', 'warning', "{$kb} KB — über dem Richtwert von 500 KB.");
            } else {
                $findings[] = $this->finding('performance', 'HTML-Größe', 'success', "{$kb} KB.");
            }

            $titleNodes = $crawler->filter('title');
            $title = $titleNodes->count() > 0 ? trim($titleNodes->text()) : '';
            $titleLen = mb_strlen($title);
            if ($title === '') {
                $score -= 15;
                $findings[] = $this->finding('seo', 'Seitentitel', 'error', 'Kein <title>-Tag. Suchmaschinen zeigen dann die URL.', 'Einen Titel mit 50–60 Zeichen setzen.');
            } elseif ($titleLen < 15 || $titleLen > 75) {
                $score -= 5;
                $findings[] = $this->finding('seo', 'Seitentitel', 'warning', "{$titleLen} Zeichen — außerhalb von 15–75.", 'Titel auf etwa 50–60 Zeichen kürzen oder ergänzen.');
            } else {
                $findings[] = $this->finding('seo', 'Seitentitel', 'success', $title);
            }

            $metaDescNodes = $crawler->filter('meta[name="description"], meta[property="og:description"]');
            $metaDesc = $metaDescNodes->count() > 0 ? trim((string) ($metaDescNodes->attr('content') ?? '')) : '';
            if ($metaDesc === '') {
                $score -= 10;
                $findings[] = $this->finding('seo', 'Meta-Description', 'warning', 'Keine Description. Snippets in der Suche bleiben leer.', '120–160 Zeichen setzen.');
            } else {
                $findings[] = $this->finding('seo', 'Meta-Description', 'success', mb_strlen($metaDesc).' Zeichen vorhanden.');
            }

            $h1Count = $crawler->filter('h1')->count();
            if ($h1Count === 0) {
                $score -= 10;
                $findings[] = $this->finding('seo', 'H1-Überschrift', 'error', 'Keine H1 auf der Seite.', 'Genau eine H1 setzen, die das Seitenthema nennt.');
            } elseif ($h1Count > 1) {
                $score -= 3;
                $findings[] = $this->finding('seo', 'H1-Überschrift', 'warning', "{$h1Count} H1-Tags. Eine H1 pro Seite ist die Regel.");
            } else {
                $findings[] = $this->finding('seo', 'H1-Überschrift', 'success', 'Eine H1 vorhanden.');
            }

            if ($crawler->filter('meta[name="viewport"]')->count() === 0) {
                $score -= 10;
                $findings[] = $this->finding('seo', 'Viewport', 'error', 'Kein Viewport-Meta. Mobilansicht bricht.', 'content="width=device-width, initial-scale=1" setzen.');
            } else {
                $findings[] = $this->finding('seo', 'Viewport', 'success', 'Viewport-Meta vorhanden.');
            }

            $images = $crawler->filter('img');
            $totalImages = $images->count();
            $missingAlt = 0;
            if ($totalImages > 0) {
                foreach ($images as $img) {
                    $alt = (new Crawler($img))->attr('alt');
                    if ($alt === null || trim($alt) === '') {
                        $missingAlt++;
                    }
                }
            }

            if ($missingAlt > 0) {
                $score -= min(15, $missingAlt * 3);
                $findings[] = $this->finding(
                    'accessibility',
                    'Bild-Alternativtexte',
                    $missingAlt > 3 ? 'error' : 'warning',
                    "{$missingAlt} von {$totalImages} Bildern ohne alt.",
                    'Jedes inhaltliche Bild braucht einen kurzen alt-Text.'
                );
            } elseif ($totalImages > 0) {
                $findings[] = $this->finding('accessibility', 'Bild-Alternativtexte', 'success', "{$totalImages} Bilder mit alt.");
            }

            $isHttps = str_starts_with($url, 'https://');
            if (! $isHttps) {
                $score -= 20;
                $findings[] = $this->finding('security', 'HTTPS', 'error', 'Kein HTTPS.', 'Zertifikat setzen und HTTP auf HTTPS umleiten.');
            } else {
                $findings[] = $this->finding('security', 'HTTPS', 'success', 'Verbindung über HTTPS.');
            }

            $hasHsts = isset($headers['strict-transport-security']);
            $hasXFrame = isset($headers['x-frame-options']);
            $hasCsp = isset($headers['content-security-policy']);

            if (! $hasHsts && $isHttps) {
                $score -= 5;
                $findings[] = $this->finding('security', 'HSTS', 'warning', 'Header Strict-Transport-Security fehlt.', 'HSTS in Nginx/Apache setzen, damit Browser HTTPS merken.');
            } elseif ($hasHsts) {
                $findings[] = $this->finding('security', 'HSTS', 'success', 'HSTS-Header gesetzt.');
            }

            if (! $hasXFrame && ! $hasCsp) {
                $findings[] = $this->finding('security', 'Frame-Schutz', 'warning', 'Weder X-Frame-Options noch CSP.', 'Einen der beiden Header setzen, sonst ist Einbetten in fremde Frames möglich.');
            } else {
                $findings[] = $this->finding('security', 'Frame-Schutz', 'success', $hasXFrame ? 'X-Frame-Options gesetzt.' : 'CSP gesetzt.');
            }

        } catch (\Throwable $e) {
            Log::error("SpectoraAuditEngine exception for {$url}: ".$e->getMessage());
            $score = 0;
            $findings[] = $this->finding('performance', 'Audit abgebrochen', 'error', $e->getMessage());
        }

        $finalScore = max(0, min(100, $score));

        return new AuditResult(
            score: $finalScore,
            grade: self::calculateGrade($finalScore),
            metrics: [
                'ttfb_ms' => (int) round($ttfb * 1000),
                'total_time_ms' => (int) round($totalTime * 1000),
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

    /**
     * @return array{category: string, label: string, status: string, message: string, recommendation?: string}
     */
    private function finding(string $category, string $label, string $status, string $message, ?string $recommendation = null): array
    {
        $row = [
            'category' => $category,
            'label' => $label,
            'status' => $status,
            'message' => $message,
        ];
        if ($recommendation !== null) {
            $row['recommendation'] = $recommendation;
        }

        return $row;
    }
}
