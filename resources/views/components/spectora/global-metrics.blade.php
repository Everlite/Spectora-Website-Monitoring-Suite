@props(['kpis'])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Card 1: Monitored Websites -->
    <div class="glass-card p-5 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg class="w-16 h-16 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
            </svg>
        </div>
        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
            Monitored Websites
        </div>
        <div class="text-3xl font-extrabold text-white font-mono tracking-tight">
            {{ $kpis['total_websites'] ?? 0 }}
        </div>
        <div class="mt-2 text-xs text-slate-400 flex items-center gap-1.5">
            <span class="text-emerald-400 font-semibold">{{ $kpis['online_count'] ?? 0 }} Online</span>
            <span>·</span>
            <span>15m check cycle</span>
        </div>
    </div>

    <!-- Card 2: Global 30d Uptime -->
    <div class="glass-card p-5 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg class="w-16 h-16 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
        </div>
        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            Average Uptime (30d)
        </div>
        <div class="text-3xl font-extrabold font-mono tracking-tight {{ ($kpis['global_uptime'] ?? 100) >= 99 ? 'text-emerald-400' : 'text-amber-400' }}">
            {{ number_format($kpis['global_uptime'] ?? 100, 1) }}%
        </div>
        <div class="mt-2 text-xs text-slate-400">
            Across all active probe intervals
        </div>
    </div>

    <!-- Card 3: Active Incidents -->
    <div class="glass-card p-5 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg class="w-16 h-16 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'bg-rose-500 animate-ping' : 'bg-emerald-500' }}"></span>
            Active Incidents
        </div>
        <div class="text-3xl font-extrabold font-mono tracking-tight {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'text-rose-400' : 'text-slate-200' }}">
            {{ $kpis['active_incidents'] ?? 0 }}
        </div>
        <div class="mt-2 text-xs text-slate-400">
            @if(($kpis['active_incidents'] ?? 0) === 0)
                <span class="text-emerald-400 font-semibold">● All systems healthy</span>
            @else
                <span class="text-rose-400 font-semibold">Requires immediate attention</span>
            @endif
        </div>
    </div>

    <!-- Card 4: Pulse Telemetry (24h Traffic) -->
    <div class="glass-card p-5 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
            <svg class="w-16 h-16 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </div>
        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-violet-400"></span>
            Pulse Visitors (24h)
        </div>
        <div class="text-3xl font-extrabold text-white font-mono tracking-tight">
            {{ number_format($kpis['total_visitors_today'] ?? 0) }}
        </div>
        <div class="mt-2 text-xs text-slate-400 flex items-center gap-1">
            <span class="text-cyan-400 font-semibold">Cookie-free</span> First-party telemetry
        </div>
    </div>
</div>
