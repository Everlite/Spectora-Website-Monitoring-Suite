@php
    $visitors30 = (int) array_sum($chartVisitors ?? []);
    $pageviews30 = (int) array_sum($chartPageviews ?? []);
    $pagesPerVisit = $visitors30 > 0 ? round($pageviews30 / $visitors30, 1) : 0.0;
    $sslDays = $sslDaysRemaining ?? null;
    $sslOk = is_numeric($sslDays) && (int) $sslDays >= 14;
    $statusCode = (int) ($domain->status_code ?? 0);
    $watchdogOk = ($domain->safety_status ?? 'safe') === 'safe';
@endphp

{{-- Pulse first: this page is a property report, not an ops cockpit --}}
<div class="space-y-6">

    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-mono text-studio-muted">
        <span class="inline-flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full {{ $statusCode >= 200 && $statusCode < 400 ? 'bg-studio-emerald' : 'bg-studio-rose' }}"></span>
            {{ $statusCode >= 200 && $statusCode < 400 ? 'Erreichbar' : 'Down' }}
            @if($statusCode > 0) · {{ $statusCode }}@endif
        </span>
        <span>Uptime {{ number_format($uptime ?? 0, 1) }}%</span>
        <span class="{{ $sslOk ? 'text-studio-muted' : 'text-studio-rose' }}">
            SSL {{ is_numeric($sslDays) ? (int) $sslDays.' Tage' : '—' }}
        </span>
        <span>{{ is_numeric($avgResponseTime ?? null) ? (int) $avgResponseTime.' ms' : '— ms' }}</span>
        <span>Score {{ is_numeric($score ?? null) ? (int) $score : '—' }}</span>
        <span class="{{ $watchdogOk ? 'text-studio-emerald' : 'text-studio-amber' }}">
            Watchdog {{ $watchdogOk ? 'sauber' : 'Hinweise' }}
        </span>
        @if($domain->last_checked)
            <span class="ml-auto">Letzter Check {{ $domain->last_checked->diffForHumans() }}</span>
        @endif
    </div>

    <div>
        <p class="text-[10px] font-bold uppercase tracking-widest text-studio-muted mb-3">Spectora Pulse · 30 Tage</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="spectora-card p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-studio-muted">Besucher</p>
                <p class="mt-2 text-3xl font-bold text-white tabular-nums">{{ number_format($visitors30) }}</p>
                <p class="mt-1 text-[11px] text-studio-muted">täglich neu gehashte Besucher-IDs, kein Cookie</p>
            </div>
            <div class="spectora-card p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-studio-muted">Seitenaufrufe</p>
                <p class="mt-2 text-3xl font-bold text-white tabular-nums">{{ number_format($pageviews30) }}</p>
                <p class="mt-1 text-[11px] text-studio-muted">Pageviews aus dem Pulse-Snippet</p>
            </div>
            <div class="spectora-card p-5">
                <p class="text-[10px] font-bold uppercase tracking-widest text-studio-muted">Seiten / Besuch</p>
                <p class="mt-2 text-3xl font-bold text-white tabular-nums">{{ $pagesPerVisit }}</p>
                <p class="mt-1 text-[11px] text-studio-muted">Aufrufe geteilt durch Besucher</p>
            </div>
        </div>
    </div>

    <div class="spectora-card p-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-4">
            <h3 class="text-sm font-semibold text-white">Verlauf</h3>
            <div class="flex items-center gap-4 text-xs font-mono">
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-studio-brand"></div>
                    <span class="text-studio-muted">Besucher</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-studio-emerald"></div>
                    <span class="text-studio-muted">Aufrufe</span>
                </div>
            </div>
        </div>
        <div class="relative h-64 w-full">
            <canvas id="overviewChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="spectora-card overflow-hidden">
            <div class="px-5 py-3 border-b border-studio-border">
                <h3 class="text-sm font-semibold text-white">Seiten</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-[10px] uppercase tracking-wider text-studio-muted">
                            <th class="px-5 py-2 font-medium">Pfad</th>
                            <th class="px-5 py-2 font-medium text-right">Aufrufe</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-studio-border">
                        @forelse(($topPages ?? []) as $p)
                            <tr>
                                <td class="px-5 py-2.5 font-mono text-xs text-white truncate max-w-xs">{{ $p->url ?? '/' }}</td>
                                <td class="px-5 py-2.5 text-right font-mono text-white">{{ number_format($p->total ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-5 py-8 text-center text-studio-muted">Noch keine Seitenaufrufe.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="spectora-card overflow-hidden">
            <div class="px-5 py-3 border-b border-studio-border">
                <h3 class="text-sm font-semibold text-white">Quellen</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-[10px] uppercase tracking-wider text-studio-muted">
                            <th class="px-5 py-2 font-medium">Referrer</th>
                            <th class="px-5 py-2 font-medium text-right">Aufrufe</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-studio-border">
                        @forelse(($topSources ?? []) as $s)
                            <tr>
                                <td class="px-5 py-2.5 text-white truncate max-w-xs">{{ $s->referrer_domain ?? 'Direkt' }}</td>
                                <td class="px-5 py-2.5 text-right font-mono text-white">{{ number_format($s->total ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-5 py-8 text-center text-studio-muted">Noch keine Referrer.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="spectora-card p-5">
            <h3 class="text-sm font-semibold text-white mb-3">Geräte</h3>
            <div class="flex items-center justify-center py-2">
                <div class="relative w-36 h-36">
                    <canvas id="overviewDeviceChart"></canvas>
                </div>
            </div>
            <div class="mt-3 space-y-2 border-t border-studio-border pt-3">
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-studio-brand"></div>
                        <span class="text-studio-muted">Desktop</span>
                    </div>
                    <span class="font-mono font-bold text-white">{{ $deviceStats['desktop'] ?? 0 }}%</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-studio-emerald"></div>
                        <span class="text-studio-muted">Mobile</span>
                    </div>
                    <span class="font-mono font-bold text-white">{{ $deviceStats['mobile'] ?? 0 }}%</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-studio-amber"></div>
                        <span class="text-studio-muted">Tablet</span>
                    </div>
                    <span class="font-mono font-bold text-white">{{ $deviceStats['tablet'] ?? 0 }}%</span>
                </div>
            </div>
        </div>

        <div class="spectora-card overflow-hidden">
            <div class="px-5 py-3 border-b border-studio-border">
                <h3 class="text-sm font-semibold text-white">Länder</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-studio-border">
                        @forelse(($topCountries ?? []) as $c)
                            <tr>
                                <td class="px-5 py-2.5 text-white">{{ $c->country }}</td>
                                <td class="px-5 py-2.5 text-right font-mono text-white">{{ number_format($c->total ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr><td class="px-5 py-8 text-center text-studio-muted">Noch keine Geo-Daten.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="spectora-card overflow-hidden">
            <div class="px-5 py-3 border-b border-studio-border">
                <h3 class="text-sm font-semibold text-white">Städte</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-studio-border">
                        @forelse(($topCities ?? []) as $c)
                            <tr>
                                <td class="px-5 py-2.5 text-white">
                                    {{ $c->city }}
                                    <span class="text-studio-muted text-xs">{{ $c->country }}</span>
                                </td>
                                <td class="px-5 py-2.5 text-right font-mono text-white">{{ number_format($c->total ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr><td class="px-5 py-8 text-center text-studio-muted">Noch keine Städte.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <details class="spectora-card">
        <summary class="px-5 py-3 text-sm font-semibold text-white cursor-pointer">Spectora Pulse einbinden</summary>
        <div class="px-5 pb-5">
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
