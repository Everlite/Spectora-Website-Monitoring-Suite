@props(['kpis'])

<div class="grid gap-5 md:grid-cols-2 lg:grid-cols-4">
    
    <!-- 1. Total Websites -->
    <div class="horizon-card p-4.5 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-[#1B254B] flex items-center justify-center text-[#7551FF] shrink-0 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold text-[#A3AED0]">Überwachte Websites</p>
            <h4 class="text-2xl font-extrabold text-white font-mono tracking-tight mt-0.5">
                {{ $kpis['total_websites'] ?? 0 }}
            </h4>
            <p class="text-[11px] font-bold text-[#01B574] mt-1 flex items-center gap-1">
                <span>{{ $kpis['online_count'] ?? 0 }} Online</span>
                <span class="text-[#A3AED0] font-normal">· 15m Probes</span>
            </p>
        </div>
    </div>

    <!-- 2. Fleet 30d Uptime -->
    <div class="horizon-card p-4.5 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-[#1B254B] flex items-center justify-center text-[#01B574] shrink-0 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold text-[#A3AED0]">30-Tage Flotten-Uptime</p>
            <h4 class="text-2xl font-extrabold font-mono tracking-tight mt-0.5 {{ ($kpis['global_uptime'] ?? 100) >= 99 ? 'text-[#01B574]' : 'text-[#FFB547]' }}">
                {{ number_format($kpis['global_uptime'] ?? 100, 1) }}%
            </h4>
            <p class="text-[11px] font-medium text-[#A3AED0] mt-1">
                SLA Gesamtstatus
            </p>
        </div>
    </div>

    <!-- 3. Active Incidents -->
    <div class="horizon-card p-4.5 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-[#1B254B] flex items-center justify-center shrink-0 shadow-inner {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'text-[#EE5D50]' : 'text-[#A3AED0]' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold text-[#A3AED0]">Aktive Störungen</p>
            <h4 class="text-2xl font-extrabold font-mono tracking-tight mt-0.5 {{ ($kpis['active_incidents'] ?? 0) > 0 ? 'text-[#EE5D50]' : 'text-white' }}">
                {{ $kpis['active_incidents'] ?? 0 }}
            </h4>
            <p class="text-[11px] font-bold mt-1 {{ ($kpis['active_incidents'] ?? 0) === 0 ? 'text-[#01B574]' : 'text-[#EE5D50]' }}">
                {{ ($kpis['active_incidents'] ?? 0) === 0 ? '✓ Alle Systeme gesund' : '⚠️ Sofortige Prüfung' }}
            </p>
        </div>
    </div>

    <!-- 4. Pulse Telemetry -->
    <div class="horizon-card p-4.5 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-[#1B254B] flex items-center justify-center text-[#3965FF] shrink-0 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold text-[#A3AED0]">Besucher (24h Pulse)</p>
            <h4 class="text-2xl font-extrabold text-white font-mono tracking-tight mt-0.5">
                {{ number_format($kpis['total_visitors_today'] ?? 0) }}
            </h4>
            <p class="text-[11px] font-medium text-[#A3AED0] mt-1">
                Zero-Cookie Telemetrie
            </p>
        </div>
    </div>

</div>
