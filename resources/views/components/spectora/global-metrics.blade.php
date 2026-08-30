@props(['kpis'])

<div class="border-b border-studio-border pb-10 mb-2">
    <p class="sp-kicker">Pulse · heute</p>
    <div class="mt-3 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8">
        <p class="sp-display text-7xl sm:text-8xl text-studio-text tabular-nums">
            {{ number_format($kpis['total_visitors_today'] ?? 0) }}
        </p>
        <div class="flex flex-wrap gap-x-10 gap-y-3 text-sm text-studio-muted pb-1">
            <div>
                <div class="sp-kicker mb-1">Websites</div>
                <div class="text-studio-text font-medium">{{ $kpis['total_websites'] ?? 0 }}</div>
            </div>
            <div>
                <div class="sp-kicker mb-1">Erreichbar</div>
                <div class="text-studio-emerald font-medium">{{ $kpis['online_count'] ?? 0 }}</div>
            </div>
            <div>
                <div class="sp-kicker mb-1">Uptime 30d</div>
                <div class="text-studio-text font-medium">{{ number_format($kpis['global_uptime'] ?? 100, 1) }}%</div>
            </div>
            <div>
                <div class="sp-kicker mb-1">Störungen</div>
                <div class="{{ ($kpis['active_incidents'] ?? 0) > 0 ? 'text-studio-rose' : 'text-studio-text' }} font-medium">
                    {{ $kpis['active_incidents'] ?? 0 }}
                </div>
            </div>
        </div>
    </div>
</div>
