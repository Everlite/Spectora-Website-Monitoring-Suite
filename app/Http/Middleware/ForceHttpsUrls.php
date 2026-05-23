<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsUrls
{
    /**
     * Handle an incoming request.
     *
     * Setzt den Request auf mehreren Ebenen auf HTTPS:
     * 1. $_SERVER['HTTPS'] = 'on'  →  $request->isSecure() liefert TRUE
     * 2. X-Forwarded-Proto Header   →  TrustProxies-Erkennung
     * 3. URL::forceScheme/forceRootUrl → URL-Generator-Override
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ebene 1: Server-Variablen – macht den Request nativ "secure"
        $request->server->set('HTTPS', 'on');
        $request->server->set('SERVER_PORT', 443);

        // Ebene 2: Proxy-Header – für TrustProxies
        $request->headers->set('X-Forwarded-Proto', 'https');
        $request->headers->set('X-Forwarded-Port', 443);

        // Ebene 3: URL-Generator – direktes Override
        URL::forceScheme('https');
        URL::forceRootUrl(config('app.url'));

        /** @var Response $response */
        $response = $next($request);

        // Verhindert Caching durch NPM/Browser – stellt sicher, dass
        // keine alte HTML-Version mit http-URLs ausgeliefert wird.
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
