@props(['domain'])

@php
    $isOnline = $domain->status_code >= 200 && $domain->status_code < 400;
    $sslDays = $domain->ssl_days_left ?? 0;
    $uptime = $domain->calculated_uptime ?? $domain->calculateUptime();
    $score = $domain->pagespeed_score_desktop ?? 0;
    $grade = $domain->grade ?? 'F';
@endphp

<div class="premium-card flex flex-col justify-between group overflow-hidden" 
     data-domain-url="{{ strtolower($domain->url) }}"
     data-domain-status="{{ $isOnline ? 'online' : 'offline' }}"
     data-domain-ssl="{{ $sslDays <= 14 ? 'expiring' : 'ok' }}">
    
    <div class="p-5">
        <!-- Header -->
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="relative flex h-2 w-2">
                        @if($isOnline)
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        @else
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                        @endif
                    </span>
                    <h3 class="text-sm font-semibold text-white truncate hover:text-blue-400 transition-colors" title="{{ $domain->url }}">
                        <a href="{{ route('domains.show', $domain) }}">{{ $domain->url }}</a>
                    </h3>
                </div>
                <div class="text-[11px] text-slate-400">
                    Geprüft vor {{ $domain->last_checked ? $domain->last_checked->diffForHumans(null, true) : 'nie' }}
                </div>
            </div>

            <!-- Grade & Tracking Code Quick Actions -->
            <div class="flex items-center gap-1.5">
                @if($score > 0)
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono tracking-wider
                        @if($score >= 90) bg-emerald-950/60 text-emerald-400 border border-emerald-800/40
                        @elseif($score >= 75) bg-blue-950/60 text-blue-400 border border-blue-800/40
                        @elseif($score >= 50) bg-amber-950/60 text-amber-400 border border-amber-800/40
                        @else bg-rose-950/60 text-rose-400 border border-rose-800/40
                        @endif">
                        Grade {{ $grade }}
                    </span>
                @endif

                <!-- Tracking Code Button -->
                <button type="button" 
                        @click="openTracking({{ json_encode($domain->url) }}, {{ json_encode($domain->uuid) }})"
                        class="p-1.5 rounded-lg bg-slate-800/60 hover:bg-slate-700/80 text-slate-300 hover:text-white border border-slate-700/50 transition-colors"
                        title="Pulse Tracking Code anzeigen">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                </button>
            </div>
        </div>

        <!-- Status & Watchdog Badges -->
        <div class="flex flex-wrap items-center gap-2 mb-4">
            @if($isOnline)
                <span class="badge-status-online">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    Online ({{ $domain->status_code }})
                </span>
            @else
                <span class="badge-status-offline">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                    {{ $domain->status_code ? 'HTTP '.$domain->status_code : 'Nicht erreichbar' }}
                </span>
            @endif

            <!-- Watchdog Button -->
            <button type="button" 
                    @click="openWatchdog({{ json_encode($domain->url) }}, {{ json_encode($domain->safety_details ?? []) }}, {{ json_encode($domain->safety_status) }})"
                    class="cursor-pointer transition-colors
                    @if($domain->safety_status === 'safe') badge-status-neutral
                    @elseif($domain->safety_status === 'danger') badge-status-offline
                    @else badge-status-warning
                    @endif">
                @if($domain->safety_status === 'safe')
                    <span>Watchdog: Sicher</span>
                @elseif($domain->safety_status === 'danger')
                    <span>⚠️ Bedrohung erkannt</span>
                @else
                    <span>Watchdog: Warnung</span>
                @endif
            </button>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 gap-2.5 bg-[#070C18] border border-[#1E293B] rounded-lg p-3 mb-4">
            <!-- 30d Uptime -->
            <div>
                <div class="text-[10px] uppercase font-semibold text-slate-500 tracking-wider">Uptime (30d)</div>
                <div class="font-mono text-xs font-bold text-white mt-0.5">
                    <span class="{{ $uptime >= 99 ? 'text-emerald-400' : ($uptime >= 95 ? 'text-amber-400' : 'text-rose-400') }}">
                        {{ number_format($uptime, 1) }}%
                    </span>
                </div>
            </div>

            <!-- Response Time -->
            <div>
                <div class="text-[10px] uppercase font-semibold text-slate-500 tracking-wider">Latenz</div>
                <div class="font-mono text-xs font-bold text-slate-200 mt-0.5">
                    @if(isset($domain->response_time))
                        @if($domain->response_time < 1)
                            {{ round($domain->response_time * 1000) }} ms
                        @else
                            {{ number_format($domain->response_time, 2) }} s
                        @endif
                    @else
                        --
                    @endif
                </div>
            </div>

            <!-- SSL Expiry -->
            <div>
                <div class="text-[10px] uppercase font-semibold text-slate-500 tracking-wider">SSL Zertifikat</div>
                <div class="font-mono text-xs font-medium mt-0.5 {{ $sslDays > 30 ? 'text-slate-300' : ($sslDays > 7 ? 'text-amber-400' : 'text-rose-400') }}">
                    {{ $sslDays > 0 ? $sslDays.' Tage' : ($sslDays === 0 ? 'Läuft ab' : 'Kein SSL') }}
                </div>
            </div>

            <!-- Visitors -->
            <div>
                <div class="text-[10px] uppercase font-semibold text-slate-500 tracking-wider">Besucher (24h)</div>
                <div class="font-mono text-xs font-semibold text-slate-300 mt-0.5">
                    {{ $domain->visitors_count_today ?? $domain->visitors_today ?? 0 }}
                </div>
            </div>
        </div>
    </div>

    <!-- Action Footer -->
    <div class="bg-[#0B1120] border-t border-[#1E293B] px-4 py-2.5 flex items-center justify-between gap-2">
        <a href="{{ route('domains.show', $domain) }}" class="btn-premium-secondary text-xs flex-1 text-center justify-center py-1.5">
            Dashboard
        </a>

        <!-- Notes Button -->
        <button type="button" 
                @click="openNotes({{ $domain->id }}, {{ json_encode($domain->url) }})" 
                class="p-1.5 rounded-lg bg-slate-800/40 border border-slate-700/50 text-slate-300 hover:text-white transition-colors" 
                title="Notizen">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        </button>

        <!-- Delete Button -->
        <form method="POST" action="{{ route('domains.destroy', $domain) }}" id="delete-form-{{ $domain->id }}">
            @csrf
            @method('DELETE')
            <button type="button" 
                    @click="confirmDelete('domain', 'delete-form-{{ $domain->id }}', {{ json_encode($domain->url) }})"
                    class="btn-premium-danger py-1 px-2" title="Entfernen">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </form>
    </div>
</div>
