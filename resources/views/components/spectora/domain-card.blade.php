@props(['domain'])

@php
    $isOnline = $domain->status_code >= 200 && $domain->status_code < 400;
    $sslDays = $domain->ssl_days_left ?? 0;
    $uptime = $domain->calculated_uptime ?? $domain->calculateUptime();
    $score = $domain->pagespeed_score_desktop ?? 0;
    $grade = $domain->grade ?? 'F';
@endphp

<div class="spectora-card p-4.5 flex flex-col justify-between" 
     data-domain-url="{{ strtolower($domain->url) }}"
     data-domain-status="{{ $isOnline ? 'online' : 'offline' }}">
    
    <div>
        <!-- Top Row -->
        <div class="flex items-start justify-between gap-3 mb-3">
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="relative flex h-2 w-2 shrink-0">
                        <span class="relative inline-flex rounded-full h-2 w-2 {{ $isOnline ? 'bg-studio-emerald' : 'bg-studio-rose' }}"></span>
                    </span>
                    <h3 class="text-xs font-bold text-white truncate hover:text-studio-brand transition-colors">
                        <a href="{{ route('domains.show', $domain) }}">{{ $domain->url }}</a>
                    </h3>
                </div>
                <div class="text-[10px] text-studio-muted">
                    Vor {{ $domain->last_checked ? $domain->last_checked->diffForHumans(null, true) : 'nie' }}
                </div>
            </div>

            <!-- Grade & Code -->
            <div class="flex items-center gap-1.5 shrink-0">
                @if($score > 0)
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono tracking-wider
                        @if($score >= 90) bg-studio-emerald/15 text-studio-emerald border border-studio-emerald/30
                        @elseif($score >= 75) bg-studio-brand/15 text-studio-brand-hover border border-studio-brand/30
                        @elseif($score >= 50) bg-studio-amber/15 text-studio-amber border border-studio-amber/30
                        @else bg-studio-rose/15 text-studio-rose border border-studio-rose/30
                        @endif">
                        Grade {{ $grade }}
                    </span>
                @endif

                <button type="button" 
                        @click="openTracking({{ json_encode($domain->url) }}, {{ json_encode($domain->uuid) }})"
                        class="p-1 rounded-studio-sm bg-studio-elevated hover:bg-studio-border text-studio-muted hover:text-white transition-colors border border-studio-border"
                        title="Pulse Tracking Code">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                </button>
            </div>
        </div>

        <!-- Status Badges -->
        <div class="flex flex-wrap items-center gap-2 mb-3">
            @if($isOnline)
                <span class="badge-status-online">
                    ● Online ({{ $domain->status_code }})
                </span>
            @else
                <span class="badge-status-offline">
                    ● {{ $domain->status_code ? 'HTTP '.$domain->status_code : 'Offline' }}
                </span>
            @endif

            <button type="button" 
                    @click="openWatchdog({{ json_encode($domain->url) }}, {{ json_encode($domain->safety_details ?? []) }}, {{ json_encode($domain->safety_status) }})"
                    class="cursor-pointer
                    @if($domain->safety_status === 'safe') badge-status-neutral
                    @elseif($domain->safety_status === 'danger') badge-status-offline
                    @else badge-status-warning
                    @endif">
                @if($domain->safety_status === 'safe')
                    <span>✓ Sicher</span>
                @elseif($domain->safety_status === 'danger')
                    <span>⚠️ Alarm</span>
                @else
                    <span>Warnung</span>
                @endif
            </button>
        </div>

        <!-- Metrics Inset Grid -->
        <div class="grid grid-cols-2 gap-2 bg-studio-bg border border-studio-border rounded-studio-sm p-2.5 mb-3">
            <div>
                <div class="text-[10px] uppercase font-bold text-studio-muted">30d Uptime</div>
                <div class="font-mono text-xs font-bold mt-0.5 {{ $uptime >= 99 ? 'text-studio-emerald' : ($uptime >= 95 ? 'text-studio-amber' : 'text-studio-rose') }}">
                    {{ number_format($uptime, 1) }}%
                </div>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-studio-muted">Latenz</div>
                <div class="font-mono text-xs font-bold text-white mt-0.5">
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
            <div>
                <div class="text-[10px] uppercase font-bold text-studio-muted">SSL Zertifikat</div>
                <div class="font-mono text-[11px] font-medium mt-0.5 {{ $sslDays > 30 ? 'text-white' : ($sslDays > 7 ? 'text-studio-amber' : 'text-studio-rose') }}">
                    {{ $sslDays > 0 ? $sslDays.' d' : ($sslDays === 0 ? 'Expiring' : 'Kein SSL') }}
                </div>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-studio-muted">Besucher (24h)</div>
                <div class="font-mono text-xs font-bold text-white mt-0.5">
                    {{ $domain->visitors_count_today ?? $domain->visitors_today ?? 0 }}
                </div>
            </div>
        </div>
    </div>

    <!-- Action Footer -->
    <div class="pt-2.5 border-t border-studio-border flex items-center justify-between gap-2">
        <a href="{{ route('domains.show', $domain) }}" class="btn-spectora-primary flex-1 text-center justify-center py-1.5 text-xs">
            Dashboard
        </a>

        <button type="button" 
                @click="openNotes({{ $domain->id }}, {{ json_encode($domain->url) }})" 
                class="p-1.5 rounded-studio-sm bg-studio-elevated hover:bg-studio-border text-studio-muted hover:text-white transition-colors border border-studio-border" 
                title="Notizen">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        </button>

        <form method="POST" action="{{ route('domains.destroy', $domain) }}" id="delete-form-{{ $domain->id }}">
            @csrf
            @method('DELETE')
            <button type="button" 
                    @click="confirmDelete('domain', 'delete-form-{{ $domain->id }}', {{ json_encode($domain->url) }})"
                    class="p-1.5 rounded-studio-sm bg-studio-elevated hover:bg-studio-rose/20 text-studio-muted hover:text-studio-rose transition-colors border border-studio-border" title="Entfernen">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </form>
    </div>
</div>
