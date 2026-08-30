@props(['kpis'])

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="spectora-card p-4">
        <p class="text-xs text-studio-muted">Nutzer (heute)</p>
        <p class="mt-1 text-3xl font-normal text-studio-text tabular-nums">{{ number_format($kpis['total_visitors_today'] ?? 0) }}</p>
        <p class="mt-1 text-[11px] text-studio-subtle">Pulse, ohne Cookie</p>
    </div>
    <div class="spectora-card p-4">
        <p class="text-xs text-studio-muted">Websites</p>
        <p class="mt-1 text-3xl font-normal text-studio-text tabular-nums">{{ $kpis['total_websites'] ?? 0 }}</p>
        <p class="mt-1 text-[11px] text-studio-emerald">{{ $kpis['online_count'] ?? 0 }} erreichbar</p>
    </div>
    <div class="spectora-card p-4">
        <p class="text-xs text-studio-muted">Uptime (30 Tage)</p>
        <p class="mt-1 text-3xl font-normal tabular-nums {{ ($kpis['global_uptime'] ?? 100) >= 99 ? 'text-studio-emerald' : 'text-studio-amber' }}">
            {{ number_format($kpis['global_uptime'] ?? 100, 1) }}%
        </p>
    </div>
    <div class="spectora-card p-4">
        <p class="text-xs text-studio-muted">Störungen</p>
        <p class="mt-1 text-3xl font-normal tabular-nums {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'text-studio-rose' : 'text-studio-text' }}">
            {{ $kpis['active_incidents'] ?? 0 }}
        </p>
    </div>
</div>
