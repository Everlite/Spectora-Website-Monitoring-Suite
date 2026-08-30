@props(['kpis'])

<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">

    <div class="spectora-card p-4.5">
        <span class="text-[11px] font-bold uppercase tracking-wider text-studio-muted">Besucher heute</span>
        <div class="text-2xl font-bold font-mono text-white tracking-tight mt-1.5">
            {{ number_format($kpis['total_visitors_today'] ?? 0) }}
        </div>
        <p class="text-[11px] text-studio-muted mt-1 font-medium">
            täglich neu gehashte IDs, kein Cookie
        </p>
    </div>

    <div class="spectora-card p-4.5">
        <span class="text-[11px] font-bold uppercase tracking-wider text-studio-muted">Websites</span>
        <div class="text-2xl font-bold font-mono text-white tracking-tight mt-1.5">
            {{ $kpis['total_websites'] ?? 0 }}
        </div>
        <p class="text-[11px] text-studio-muted mt-1 flex items-center gap-1.5 font-medium">
            <span class="text-studio-emerald font-bold">{{ $kpis['online_count'] ?? 0 }} erreichbar</span>
        </p>
    </div>

    <div class="spectora-card p-4.5">
        <span class="text-[11px] font-bold uppercase tracking-wider text-studio-muted">Uptime 30 Tage</span>
        <div class="text-2xl font-bold font-mono tracking-tight mt-1.5 {{ ($kpis['global_uptime'] ?? 100) >= 99 ? 'text-studio-emerald' : 'text-studio-amber' }}">
            {{ number_format($kpis['global_uptime'] ?? 100, 1) }}%
        </div>
        <p class="text-[11px] text-studio-muted mt-1 font-medium">
            Mittel über alle Properties
        </p>
    </div>

    <div class="spectora-card p-4.5">
        <span class="text-[11px] font-bold uppercase tracking-wider text-studio-muted">Störungen</span>
        <div class="text-2xl font-bold font-mono tracking-tight mt-1.5 {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'text-studio-rose' : 'text-white' }}">
            {{ $kpis['active_incidents'] ?? 0 }}
        </div>
        <p class="text-[11px] font-medium mt-1 {{ ($kpis['active_incidents'] ?? 0) === 0 ? 'text-studio-emerald' : 'text-studio-rose' }}">
            {{ ($kpis['active_incidents'] ?? 0) === 0 ? 'Keine Ausfälle' : 'Down oder Watchdog-Alarm' }}
        </p>
    </div>

</div>
