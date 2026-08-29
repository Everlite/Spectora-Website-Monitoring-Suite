@props(['kpis'])

<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
    
    <!-- 1. Total Websites -->
    <div class="spectora-card p-4.5">
        <div class="flex items-center justify-between pb-1.5">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#8A95A8]">Flotten-Ziele</span>
            <div class="w-7 h-7 rounded-studio-sm bg-[#171E2E] border border-[#202A3E] flex items-center justify-center text-[#3B57E8]">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
            </div>
        </div>
        <div>
            <div class="text-2xl font-bold font-mono text-white tracking-tight">
                {{ $kpis['total_websites'] ?? 0 }}
            </div>
            <p class="text-[11px] text-[#8A95A8] mt-1 flex items-center gap-1.5 font-medium">
                <span class="text-[#10B981] font-bold">{{ $kpis['online_count'] ?? 0 }} Online</span>
                <span class="text-[#5A667A]">·</span>
                <span>15 Min Intervall</span>
            </p>
        </div>
    </div>

    <!-- 2. Fleet 30d Uptime -->
    <div class="spectora-card p-4.5">
        <div class="flex items-center justify-between pb-1.5">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#8A95A8]">30-Tage SLA Uptime</span>
            <div class="w-7 h-7 rounded-studio-sm bg-[#171E2E] border border-[#202A3E] flex items-center justify-center text-[#10B981]">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div>
            <div class="text-2xl font-bold font-mono tracking-tight {{ ($kpis['global_uptime'] ?? 100) >= 99 ? 'text-[#10B981]' : 'text-[#F59E0B]' }}">
                {{ number_format($kpis['global_uptime'] ?? 100, 1) }}%
            </div>
            <p class="text-[11px] text-[#8A95A8] mt-1 font-medium">
                Durchschnitt aller Endpunkte
            </p>
        </div>
    </div>

    <!-- 3. Active Incidents -->
    <div class="spectora-card p-4.5">
        <div class="flex items-center justify-between pb-1.5">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#8A95A8]">Aktive Störungen</span>
            <div class="w-7 h-7 rounded-studio-sm bg-[#171E2E] border border-[#202A3E] flex items-center justify-center {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'text-[#F43F5E]' : 'text-[#8A95A8]' }}">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>
        <div>
            <div class="text-2xl font-bold font-mono tracking-tight {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'text-[#F43F5E]' : 'text-white' }}">
                {{ $kpis['active_incidents'] ?? 0 }}
            </div>
            <p class="text-[11px] font-medium mt-1 {{ ($kpis['active_incidents'] ?? 0) === 0 ? 'text-[#10B981]' : 'text-[#F43F5E]' }}">
                {{ ($kpis['active_incidents'] ?? 0) === 0 ? '✓ Alle Systeme normal' : 'Dringende Aktion nötig' }}
            </p>
        </div>
    </div>

    <!-- 4. Pulse Telemetry -->
    <div class="spectora-card p-4.5">
        <div class="flex items-center justify-between pb-1.5">
            <span class="text-[11px] font-bold uppercase tracking-wider text-[#8A95A8]">Pulse Telemetrie (24h)</span>
            <div class="w-7 h-7 rounded-studio-sm bg-[#171E2E] border border-[#202A3E] flex items-center justify-center text-[#0EA5E9]">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </div>
        <div>
            <div class="text-2xl font-bold font-mono text-white tracking-tight">
                {{ number_format($kpis['total_visitors_today'] ?? 0) }}
            </div>
            <p class="text-[11px] text-[#8A95A8] mt-1 font-medium">
                Datenschutzkonforme Aufrufe
            </p>
        </div>
    </div>

</div>
