@props(['kpis'])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Card 1: Monitored Websites -->
    <div class="premium-card p-4.5 relative overflow-hidden">
        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1 flex items-center justify-between">
            <span>Websites</span>
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
        </div>
        <div class="text-2xl font-extrabold text-white font-mono tracking-tight">
            {{ $kpis['total_websites'] ?? 0 }}
        </div>
        <div class="mt-2 text-[11px] text-slate-400 flex items-center gap-1.5">
            <span class="text-emerald-400 font-semibold">{{ $kpis['online_count'] ?? 0 }} Online</span>
            <span>·</span>
            <span>15m Intervall</span>
        </div>
    </div>

    <!-- Card 2: Fleet 30d Uptime -->
    <div class="premium-card p-4.5 relative overflow-hidden">
        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1 flex items-center justify-between">
            <span>Uptime (30d)</span>
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
        </div>
        <div class="text-2xl font-extrabold font-mono tracking-tight {{ ($kpis['global_uptime'] ?? 100) >= 99 ? 'text-emerald-400' : 'text-amber-400' }}">
            {{ number_format($kpis['global_uptime'] ?? 100, 1) }}%
        </div>
        <div class="mt-2 text-[11px] text-slate-400">
            Gesamtflotte der Agentur
        </div>
    </div>

    <!-- Card 3: Active Incidents -->
    <div class="premium-card p-4.5 relative overflow-hidden">
        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1 flex items-center justify-between">
            <span>Aktive Störungen</span>
            <span class="w-1.5 h-1.5 rounded-full {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'bg-rose-500' : 'bg-slate-600' }}"></span>
        </div>
        <div class="text-2xl font-extrabold font-mono tracking-tight {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'text-rose-400' : 'text-slate-300' }}">
            {{ $kpis['active_incidents'] ?? 0 }}
        </div>
        <div class="mt-2 text-[11px] text-slate-400">
            @if(($kpis['active_incidents'] ?? 0) === 0)
                <span class="text-emerald-400 font-medium">Alle Systeme gesund</span>
            @else
                <span class="text-rose-400 font-medium">Sofortige Prüfung erforderlich</span>
            @endif
        </div>
    </div>

    <!-- Card 4: Pulse Telemetry -->
    <div class="premium-card p-4.5 relative overflow-hidden">
        <div class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1 flex items-center justify-between">
            <span>Besucher (24h)</span>
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
        </div>
        <div class="text-2xl font-extrabold text-white font-mono tracking-tight">
            {{ number_format($kpis['total_visitors_today'] ?? 0) }}
        </div>
        <div class="mt-2 text-[11px] text-slate-400">
            Cookie-freie Pulse-Telemetrie
        </div>
    </div>
</div>
