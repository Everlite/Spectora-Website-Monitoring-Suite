@php
    $visitors30 = (int) array_sum($chartVisitors ?? []);
    $pageviews30 = (int) array_sum($chartPageviews ?? []);
    $pagesPerVisit = $visitors30 > 0 ? round($pageviews30 / $visitors30, 1) : 0.0;
    $sslDays = $sslDaysRemaining ?? null;
    $sslOk = is_numeric($sslDays) && (int) $sslDays >= 14;
    $statusCode = (int) ($domain->status_code ?? 0);
    $watchdogOk = ($domain->safety_status ?? 'safe') === 'safe';
@endphp

<div class="space-y-16">

    <div class="flex flex-wrap items-baseline gap-x-6 gap-y-2 text-xs text-studio-muted border-b border-studio-border pb-4">
        <span class="{{ $statusCode >= 200 && $statusCode < 400 ? 'text-studio-emerald' : 'text-studio-rose' }}">
            {{ $statusCode >= 200 && $statusCode < 400 ? 'Erreichbar' : 'Down' }}
            @if($statusCode > 0) · {{ $statusCode }}@endif
        </span>
        <span>Uptime {{ number_format($uptime ?? 0, 1) }}%</span>
        <span class="{{ $sslOk ? '' : 'text-studio-rose' }}">SSL {{ is_numeric($sslDays) ? (int) $sslDays.' Tage' : '—' }}</span>
        <span>{{ is_numeric($avgResponseTime ?? null) ? (int) $avgResponseTime.' ms' : '—' }}</span>
        <span>Dokument-Score {{ is_numeric($score ?? null) ? (int) $score : '—' }}</span>
        <span class="{{ $watchdogOk ? 'text-studio-emerald' : 'text-studio-amber' }}">Watchdog {{ $watchdogOk ? 'sauber' : 'Hinweise' }}</span>
        @if($domain->last_checked)
            <span class="sm:ml-auto">Probe {{ $domain->last_checked->diffForHumans() }}</span>
        @endif
    </div>

    <div>
        <p class="sp-kicker">Spectora Pulse · 30 Tage</p>
        <div class="mt-4 grid grid-cols-1 lg:grid-cols-12 gap-10 items-end">
            <div class="lg:col-span-7">
                <p class="text-xs text-studio-muted mb-2">Besucher</p>
                <p class="sp-display text-7xl sm:text-8xl text-studio-text tabular-nums">{{ number_format($visitors30) }}</p>
                <p class="text-sm text-studio-muted mt-3">Täglich neu gehashte IDs. Kein Cookie, keine Roh-IP.</p>
            </div>
            <div class="lg:col-span-5 grid grid-cols-2 gap-8">
                <div>
                    <p class="sp-kicker mb-2">Seitenaufrufe</p>
                    <p class="sp-display text-4xl text-studio-text tabular-nums">{{ number_format($pageviews30) }}</p>
                </div>
                <div>
                    <p class="sp-kicker mb-2">Seiten / Besuch</p>
                    <p class="sp-display text-4xl text-studio-text tabular-nums">{{ $pagesPerVisit }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-studio-border pt-8">
        <div class="flex items-end justify-between mb-6">
            <h3 class="sp-kicker">Verlauf</h3>
            <div class="flex gap-5 text-xs text-studio-muted">
                <span class="text-studio-brand">Besucher</span>
                <span class="text-studio-emerald">Aufrufe</span>
            </div>
        </div>
        <div class="relative h-72 w-full">
            <canvas id="overviewChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 border-t border-studio-border pt-10">
        <div>
            <h3 class="sp-kicker mb-4">Seiten</h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-studio-border">
                    @forelse(($topPages ?? []) as $p)
                        <tr>
                            <td class="py-3 font-mono text-xs text-studio-text truncate max-w-xs">{{ $p->url ?? '/' }}</td>
                            <td class="py-3 text-right font-mono text-studio-text">{{ number_format($p->total ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-8 text-studio-muted">Noch keine Seitenaufrufe.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>
            <h3 class="sp-kicker mb-4">Quellen</h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-studio-border">
                    @forelse(($topSources ?? []) as $s)
                        <tr>
                            <td class="py-3 text-studio-text truncate max-w-xs">{{ $s->referrer_domain ?? 'Direkt' }}</td>
                            <td class="py-3 text-right font-mono text-studio-text">{{ number_format($s->total ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-8 text-studio-muted">Noch keine Referrer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 border-t border-studio-border pt-10">
        <div>
            <h3 class="sp-kicker mb-4">Geräte</h3>
            <div class="relative w-40 h-40 mx-auto">
                <canvas id="overviewDeviceChart"></canvas>
            </div>
            <div class="mt-4 space-y-2 text-xs">
                <div class="flex justify-between"><span class="text-studio-muted">Desktop</span><span class="font-mono">{{ $deviceStats['desktop'] ?? 0 }}%</span></div>
                <div class="flex justify-between"><span class="text-studio-muted">Mobile</span><span class="font-mono">{{ $deviceStats['mobile'] ?? 0 }}%</span></div>
                <div class="flex justify-between"><span class="text-studio-muted">Tablet</span><span class="font-mono">{{ $deviceStats['tablet'] ?? 0 }}%</span></div>
            </div>
        </div>
        <div>
            <h3 class="sp-kicker mb-4">Länder</h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-studio-border">
                    @forelse(($topCountries ?? []) as $c)
                        <tr>
                            <td class="py-2.5 text-studio-text">{{ $c->country }}</td>
                            <td class="py-2.5 text-right font-mono">{{ number_format($c->total ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-8 text-studio-muted">Noch keine Geo-Daten.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div>
            <h3 class="sp-kicker mb-4">Städte</h3>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-studio-border">
                    @forelse(($topCities ?? []) as $c)
                        <tr>
                            <td class="py-2.5 text-studio-text">{{ $c->city }} <span class="text-studio-muted">{{ $c->country }}</span></td>
                            <td class="py-2.5 text-right font-mono">{{ number_format($c->total ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr><td class="py-8 text-studio-muted">Noch keine Städte.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <details class="border-t border-studio-border pt-8">
        <summary class="sp-kicker cursor-pointer text-studio-text">Spectora Pulse einbinden</summary>
        <div class="mt-5">
            @include('domains.dashboard.partials.pulse-snippet')
        </div>
    </details>

    <x-spectora.engine-report
        :findings="$auditDetails ?? []"
        :watchdog="$watchdogData ?? []"
        :score="$score ?? null"
        :grade="$domain->grade ?? null"
    />
</div>
