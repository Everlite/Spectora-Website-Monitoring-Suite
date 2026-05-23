<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ForceHttpsUrls
{
    /**
     * Handle an incoming request.
     *
     * Root Cause: Nginx Proxy Manager sendet keinen X-Forwarded-Proto Header.
     * Dadurch kommt jeder Request als plain HTTP im Container an, und Laravel
     * generiert ALLE URLs (assets, forms, routes) mit http:// – unabhängig von
     * forceScheme/forceRootUrl/TrustProxies.
     *
     * Fix: Mehrstufiges Override + Response-Body-Suche-Ersetze als Fallback.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Stufe 1: Request nativ auf HTTPS zwingen
        $request->server->set('HTTPS', 'on');
        $request->server->set('SERVER_PORT', 443);
        $request->headers->set('X-Forwarded-Proto', 'https');
        $request->headers->set('X-Forwarded-Port', 443);

        // Stufe 2: URL-Generator Override
        URL::forceScheme('https');
        URL::forceRootUrl(config('app.url'));

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // Stufe 3: Response-Body-Suche-Ersetze (nuklear, aber garantiert)
        // Ersetzt ALLE http://spectora.taikon.de Vorkommen durch https://
        if (! $response instanceof BinaryFileResponse
            && ! $response instanceof StreamedResponse
            && method_exists($response, 'getContent')
        ) {
            $content = $response->getContent();

            if (is_string($content) && $content !== '') {
                $appUrl = rtrim((string) config('app.url'), '/');
                $host = parse_url($appUrl, PHP_URL_HOST);

                // Nur http:// der EIGENEN Domain ersetzen – keine Hardcoding
                if ($host) {
                    $content = str_replace(
                        'http://' . $host,
                        'https://' . $host,
                        $content
                    );
                }

                $response->setContent($content);
            }
        }

        // Anti-Cache – verhindert NPM/Browser-Caching alter HTML-Versionen
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
