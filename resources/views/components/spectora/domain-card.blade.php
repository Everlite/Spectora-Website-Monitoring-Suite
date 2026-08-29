@props(['domain'])

@php
    $isOnline = $domain->status_code >= 200 && $domain->status_code < 400;
    $sslDays = $domain->ssl_days_left ?? 0;
    $uptime = $domain->calculated_uptime ?? $domain->calculateUptime();
    $score = $domain->pagespeed_score_desktop ?? 0;
    $grade = $domain->grade ?? 'F';
@endphp

<div class="glass-card {{ $isOnline ? 'glass-card-glow-cyan' : 'glass-card-glow-rose' }} flex flex-col justify-between relative overflow-hidden group">
    
    <!-- Top Glowing Accent Stripe -->
    <div class="h-1 w-full {{ $isOnline ? 'bg-gradient-to-r from-cyan-400 via-teal-400 to-emerald-400' : 'bg-gradient-to-r from-rose-500 via-red-500 to-amber-500' }}"></div>

    <div class="p-6">
        <!-- Header: Title & Badges -->
        <div class="flex items-start justify-between gap-3 mb-4">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="relative flex h-2.5 w-2.5">
                        @if($isOnline)
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        @else
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        @endif
                    </span>
                    <h3 class="text-base font-bold text-white truncate hover:text-cyan-400 transition-colors" title="{{ $domain->url }}">
                        <a href="{{ route('domains.show', $domain) }}">{{ $domain->url }}</a>
                    </h3>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-400">
                    <span>Checked {{ $domain->last_checked ? $domain->last_checked->diffForHumans() : 'Never' }}</span>
                </div>
            </div>

            <!-- Spectora Grade Badge -->
            @if($score > 0)
                <div class="px-2.5 py-1 rounded-lg text-xs font-black tracking-wider uppercase border
                    @if($score >= 90) bg-emerald-500/10 text-emerald-400 border-emerald-500/30
                    @elseif($score >= 75) bg-cyan-500/10 text-cyan-400 border-cyan-500/30
                    @elseif($score >= 50) bg-amber-500/10 text-amber-400 border-amber-500/30
                    @else bg-rose-500/10 text-rose-400 border-rose-500/30
                    @endif" title="Spectora Engine Score: {{ $score }}/100">
                    Grade {{ $grade }}
                </div>
            @endif
        </div>

        <!-- Status Pills (Online / Offline + Watchdog) -->
        <div class="flex flex-wrap items-center gap-2 mb-5">
            <!-- HTTP Status Pill -->
            @if($isOnline)
                <span class="cyber-badge-online">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Online ({{ $domain->status_code }})
                </span>
            @else
                <span class="cyber-badge-offline">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                    {{ $domain->status_code ? 'HTTP '.$domain->status_code : 'Unreachable' }}
                </span>
            @endif

            <!-- Watchdog Security Pill -->
            <button type="button" 
                    @click="openWatchdog({{ json_encode($domain->url) }}, {{ json_encode($domain->safety_details ?? []) }}, {{ json_encode($domain->safety_status) }})"
                    class="cursor-pointer transition-transform hover:scale-105
                    @if($domain->safety_status === 'safe') cyber-badge-safe
                    @elseif($domain->safety_status === 'danger') cyber-badge-danger
                    @elseif($domain->safety_status === 'warning') cyber-badge-warning
                    @else cyber-badge-safe
                    @endif">
                @if($domain->safety_status === 'safe')
                    <svg class="w-3 h-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Watchdog: Safe</span>
                @elseif($domain->safety_status === 'danger')
                    <svg class="w-3 h-3 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Threat Detected</span>
                @else
                    <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Watchdog: Warning</span>
                @endif
            </button>
        </div>

        <!-- Telemetry & Metrics Grid -->
        <div class="grid grid-cols-2 gap-3 bg-[#0B0F17]/60 border border-slate-800/80 rounded-xl p-3.5 mb-5">
            <!-- 30d Uptime -->
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Uptime (30d)</div>
                <div class="font-mono text-sm font-bold text-white flex items-center gap-1 mt-0.5">
                    <span class="{{ $uptime >= 99 ? 'text-emerald-400' : ($uptime >= 95 ? 'text-amber-400' : 'text-rose-400') }}">
                        {{ number_format($uptime, 1) }}%
                    </span>
                </div>
            </div>

            <!-- Response Time -->
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Response Time</div>
                <div class="font-mono text-sm font-bold text-cyan-400 mt-0.5">
                    @if(isset($domain->response_time))
                        @if($domain->response_time < 1)
                            {{ round($domain->response_time * 1000) }}ms
                        @else
                            {{ number_format($domain->response_time, 2) }}s
                        @endif
                    @else
                        --
                    @endif
                </div>
            </div>

            <!-- SSL Expiry -->
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">SSL Certificate</div>
                <div class="font-mono text-sm font-bold mt-0.5 {{ $sslDays > 30 ? 'text-emerald-400' : ($sslDays > 7 ? 'text-amber-400' : 'text-rose-400') }}">
                    {{ $sslDays > 0 ? $sslDays.' Days' : ($sslDays === 0 ? 'Expiring' : 'No SSL') }}
                </div>
            </div>

            <!-- 24h Visitors -->
            <div>
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Pulse Visitors</div>
                <div class="font-mono text-sm font-bold text-slate-300 mt-0.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    {{ $domain->visitors_count_today ?? $domain->visitors_today ?? 0 }}
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Action Bar -->
    <div class="bg-[#0D1424]/90 border-t border-slate-800 px-5 py-3.5 flex items-center justify-between gap-3">
        <a href="{{ route('domains.show', $domain) }}" class="btn-cyber-primary text-xs py-2 px-3.5 flex-1 text-center justify-center">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            Dashboard
        </a>

        <!-- Quick Notes Button -->
        <button type="button" 
                @click="openNotes({{ $domain->id }}, {{ json_encode($domain->url) }})" 
                class="p-2 rounded-lg bg-slate-800/80 border border-slate-700 text-slate-300 hover:text-cyan-400 hover:border-cyan-500/40 transition-colors" 
                title="Domain Team Notes">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        </button>

        <!-- Delete Button -->
        <form method="POST" action="{{ route('domains.destroy', $domain) }}" id="delete-form-{{ $domain->id }}">
            @csrf
            @method('DELETE')
            <button type="button" 
                    @click="confirmDelete('domain', 'delete-form-{{ $domain->id }}', {{ json_encode($domain->url) }})"
                    class="btn-cyber-danger" title="Remove domain">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </form>
    </div>
</div>
