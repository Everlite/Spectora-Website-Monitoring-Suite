@php
    $visitors30 = (int) array_sum($chartVisitors ?? []);
    $pageviews30 = (int) array_sum($chartPageviews ?? []);
    $pagesPerVisit = $visitors30 > 0 ? round($pageviews30 / $visitors30, 1) : 0.0;
    $sslDays = $sslDaysRemaining ?? null;
    $sslOk = is_numeric($sslDays) && (int) $sslDays >= 14;
    $statusCode = (int) ($domain->status_code ?? 0);
    $watchdogOk = ($domain->safety_status ?? 'safe') === 'safe';
@endphp

<div class="space-y-5">

    <div class="spectora-card px-4 py-2.5 flex flex-wrap items-center gap-x-5 gap-y-1 text-xs text-studio-muted">
        <span class="{{ $statusCode >= 200 && $statusCode < 400 ? 'text-studio-emerald' : 'text-studio-rose' }}">
            {{ $statusCode >= 200 && $statusCode < 400 ? 'Aktiv' : 'Down' }}
            @if($statusCode > 0) · {{ $statusCode }}@endif
        </span>
        <span>Uptime {{ number_format($uptime ?? 0, 1) }}%</span>
        <span class="{{ $sslOk ? '' : 'text-studio-rose' }}">SSL {{ is_numeric($sslDays) ? (int) $sslDays.' Tage' : '—' }}</span>
        <span>{{ is_numeric($avgResponseTime ?? null) ? (int) $avgResponseTime.' ms' : '—' }}</span>
        <span>Dokument-Score {{ is_numeric($score ?? null) ? (int) $score : '—' }}</span>
        <span class="{{ $watchdogOk ? 'text-studio-emerald' : 'text-studio-amber' }}">Watchdog {{ $watchdogOk ? 'ok' : 'Hinweise' }}</span>
        @if($domain->last_checked)
            <span class="sm:ml-auto">Letzter Check {{ $domain->last_checked->diffForHumans() }}</span>
        @endif
    </div>

    <div>
        <h2 class="text-lg font-medium text-studio-text mb-3">Übersicht</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="spectora-card p-4">
                <p class="text-xs text-studio-muted">Besucher</p>
                <p class="mt-1 text-3xl tabular-nums text-studio-text">{{ number_format($visitors30) }}</p>
                <p class="mt-1 text-[11px] text-studio-subtle">30 Tage · Pulse, kein Cookie</p>
            </div>
            <div class="spectora-card p-4">
                <p class="text-xs text-studio-muted">Seitenaufrufe</p>
                <p class="mt-1 text-3xl tabular-nums text-studio-text">{{ number_format($pageviews30) }}</p>
            </div>
            <div class="spectora-card p-4">
                <p class="text-xs text-studio-muted">Seiten / Nutzer</p>
                <p class="mt-1 text-3xl tabular-nums text-studio-text">{{ $pagesPerVisit }}</p>
            </div>
        </div>
    </div>

    <div class="spectora-card p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-medium text-studio-text">Nutzer im Zeitverlauf</h3>
            <div class="flex gap-4 text-xs text-studio-muted">
                <span class="text-studio-brand">Nutzer</span>
                <span class="text-studio-emerald">Seitenaufrufe</span>
            </div>
        </div>
        <div class="relative h-64 w-full">
            <canvas id="overviewChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="spectora-card overflow-hidden">
            <div class="px-4 py-3 border-b border-studio-border">
                <h3 class="text-sm font-medium">Seiten und Bildschirme</h3>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-studio-muted">
                        <th class="px-4 py-2 font-medium">Seitenpfad</th>
                        <th class="px-4 py-2 font-medium text-right">Aufrufe</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-studio-border">
                    @forelse(($topPages ?? []) as $p)
                        <tr>
                            <td class="px-4 py-2.5 font-mono text-xs truncate max-w-xs">{{ $p->url ?? '/' }}</td>
                            <td class="px-4 py-2.5 text-right tabular-nums">{{ number_format($p->total ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-4 py-8 text-center text-studio-muted">Noch keine Daten. Spectora Pulse einbinden.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="spectora-card overflow-hidden">
            <div class="px-4 py-3 border-b border-studio-border">
                <h3 class="text-sm font-medium">Traffic-Quelle</h3>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-studio-muted">
                        <th class="px-4 py-2 font-medium">Quelle</th>
                        <th class="px-4 py-2 font-medium text-right">Aufrufe</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-studio-border">
                    @forelse(($topSources ?? []) as $s)
                        <tr>
                            <td class="px-4 py-2.5 truncate max-w-xs">{{ $s->referrer_domain ?? 'Direkt' }}</td>
                            <td class="px-4 py-2.5 text-right tabular-nums">{{ number_format($s->total ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="px-4 py-8 text-center text-studio-muted">Noch keine Referrer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="spectora-card p-4">
            <h3 class="text-sm font-medium mb-3">Gerät</h3>
            <div class="relative w-36 h-36 mx-auto">
                <canvas id="overviewDeviceChart"></canvas>
            </div>
            <div class="mt-3 space-y-1.5 text-xs">
                <div class="flex justify-between"><span class="text-studio-muted">Desktop</span><span>{{ $deviceStats['desktop'] ?? 0 }}%</span></div>
                <div class="flex justify-between"><span class="text-studio-muted">Mobil</span><span>{{ $deviceStats['mobile'] ?? 0 }}%</span></div>
                <div class="flex justify-between"><span class="text-studio-muted">Tablet</span><span>{{ $deviceStats['tablet'] ?? 0 }}%</span></div>
            </div>
        </div>
        <div class="spectora-card overflow-hidden">
            <div class="px-4 py-3 border-b border-studio-border"><h3 class="text-sm font-medium">Land</h3></div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-studio-border">
                    @forelse(($topCountries ?? []) as $c)
                        <tr>
                            <td class="px-4 py-2.5">{{ $c->country }}</td>
                            <td class="px-4 py-2.5 text-right tabular-nums">{{ number_format($c->total ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-8 text-center text-studio-muted">Keine Geo-Daten.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="spectora-card overflow-hidden">
            <div class="px-4 py-3 border-b border-studio-border"><h3 class="text-sm font-medium">Stadt</h3></div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-studio-border">
                    @forelse(($topCities ?? []) as $c)
                        <tr>
                            <td class="px-4 py-2.5">{{ $c->city }} <span class="text-studio-muted">{{ $c->country }}</span></td>
                            <td class="px-4 py-2.5 text-right tabular-nums">{{ number_format($c->total ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-8 text-center text-studio-muted">Keine Städte.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <details class="spectora-card">
        <summary class="px-4 py-3 text-sm font-medium cursor-pointer">Spectora Pulse einbinden</summary>
        <div class="px-4 pb-4">
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
