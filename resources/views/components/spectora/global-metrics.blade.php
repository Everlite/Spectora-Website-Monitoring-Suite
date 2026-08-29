@props(['kpis'])

<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
    
    <!-- 1. Total Websites -->
    <div class="shadcn-card p-5">
        <div class="flex items-center justify-between space-y-0 pb-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Flotten-Ziele</span>
            <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
        </div>
        <div>
            <div class="text-2xl font-bold font-mono text-foreground tracking-tight">
                {{ $kpis['total_websites'] ?? 0 }}
            </div>
            <p class="text-[11px] text-muted-foreground mt-1 flex items-center gap-1.5">
                <span class="text-emerald-400 font-semibold">{{ $kpis['online_count'] ?? 0 }} Online</span>
                <span>·</span>
                <span>Intervall: 15m</span>
            </p>
        </div>
    </div>

    <!-- 2. Fleet 30d Uptime -->
    <div class="shadcn-card p-5">
        <div class="flex items-center justify-between space-y-0 pb-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">30-Tage Uptime</span>
            <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <div class="text-2xl font-bold font-mono tracking-tight {{ ($kpis['global_uptime'] ?? 100) >= 99 ? 'text-emerald-400' : 'text-amber-400' }}">
                {{ number_format($kpis['global_uptime'] ?? 100, 1) }}%
            </div>
            <p class="text-[11px] text-muted-foreground mt-1">
                Durchschnitt aller Kunden-Websites
            </p>
        </div>
    </div>

    <!-- 3. Active Incidents -->
    <div class="shadcn-card p-5">
        <div class="flex items-center justify-between space-y-0 pb-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Aktive Störungen</span>
            <svg class="h-4 w-4 {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'text-rose-400' : 'text-muted-foreground' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div>
            <div class="text-2xl font-bold font-mono tracking-tight {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'text-rose-400' : 'text-foreground' }}">
                {{ $kpis['active_incidents'] ?? 0 }}
            </div>
            <p class="text-[11px] text-muted-foreground mt-1">
                @if(($kpis['active_incidents'] ?? 0) === 0)
                    <span class="text-emerald-400 font-medium">Alle Probes fehlerfrei</span>
                @else
                    <span class="text-rose-400 font-medium">Dringende Handlung erforderlich</span>
                @endif
            </p>
        </div>
    </div>

    <!-- 4. Pulse Telemetry -->
    <div class="shadcn-card p-5">
        <div class="flex items-center justify-between space-y-0 pb-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Pulse Telemetrie (24h)</span>
            <svg class="h-4 w-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
        </div>
        <div>
            <div class="text-2xl font-bold font-mono text-foreground tracking-tight">
                {{ number_format($kpis['total_visitors_today'] ?? 0) }}
            </div>
            <p class="text-[11px] text-muted-foreground mt-1">
                Datenschutzkonforme Seitenaufrufe
            </p>
        </div>
    </div>

</div>
