@props(['domains'])

<div class="spectora-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-studio-border bg-studio-elevated/50 text-studio-muted font-bold text-[11px] uppercase tracking-wider">
                    <th class="py-3 px-4">Website</th>
                    <th class="py-3 px-4">Besucher (heute)</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Uptime 30d</th>
                    <th class="py-3 px-4">SSL</th>
                    <th class="py-3 px-4">Watchdog</th>
                    <th class="py-3 px-4 text-right">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-studio-border">
                @foreach ($domains as $domain)
                    @php
                        $isOnline = $domain->status_code >= 200 && $domain->status_code < 400;
                        $sslDays = $domain->ssl_days_left ?? 0;
                        $uptime = $domain->calculated_uptime ?? $domain->calculateUptime();
                    @endphp
                    <tr class="hover:bg-studio-elevated/60 transition-colors"
                        x-show="matchesFilter({{ json_encode(strtolower($domain->url)) }}, {{ json_encode($isOnline ? 'online' : 'offline') }})">
                        
                        <!-- Domain Target -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2.5">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $isOnline ? 'bg-studio-emerald' : 'bg-studio-rose' }}"></span>
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('domains.show', $domain) }}" class="font-bold text-white hover:text-studio-brand transition-colors truncate block">
                                        {{ $domain->url }}
                                    </a>
                                    <span class="text-[10px] text-studio-muted">
                                        Vor {{ $domain->last_checked ? $domain->last_checked->diffForHumans(null, true) : 'nie' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="py-3.5 px-4 font-mono font-bold text-white text-sm">
                            {{ number_format($domain->visitors_count_today ?? $domain->visitors_today ?? 0) }}
                        </td>

                        <td class="py-3.5 px-4">
                            @if($isOnline)
                                <span class="badge-status-online">
                                    ● Online ({{ $domain->status_code }})
                                </span>
                            @else
                                <span class="badge-status-offline">
                                    ● {{ $domain->status_code ? 'HTTP '.$domain->status_code : 'Offline' }}
                                </span>
                            @endif
                        </td>

                        <td class="py-3.5 px-4 font-mono">
                            <span class="font-bold {{ $uptime >= 99 ? 'text-studio-emerald' : ($uptime >= 95 ? 'text-studio-amber' : 'text-studio-rose') }}">
                                {{ number_format($uptime, 1) }}%
                            </span>
                        </td>

                        <td class="py-3.5 px-4 font-mono text-xs">
                            <span class="{{ $sslDays > 30 ? 'text-studio-muted' : ($sslDays > 7 ? 'text-studio-amber' : 'text-studio-rose') }}">
                                {{ $sslDays > 0 ? $sslDays.' d' : ($sslDays === 0 ? 'Läuft ab' : 'Kein SSL') }}
                            </span>
                        </td>

                        <!-- Watchdog -->
                        <td class="py-3.5 px-4">
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
                        </td>

                        <!-- Actions -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <!-- Tracking Code Button -->
                                <button type="button" 
                                        @click="openTracking({{ json_encode($domain->url) }}, {{ json_encode($domain->uuid) }})"
                                        class="p-1.5 rounded-studio-sm bg-studio-elevated hover:bg-studio-border text-studio-muted hover:text-white transition-colors border border-studio-border"
                                        title="Pulse Tracking Code">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                </button>

                                <!-- Details Link -->
                                <a href="{{ route('domains.show', $domain) }}" 
                                   class="px-2.5 py-1 rounded-studio-sm bg-studio-elevated hover:bg-studio-brand text-white font-semibold text-xs border border-studio-border transition-colors">
                                    Bericht
                                </a>

                                <!-- Notes Button -->
                                <button type="button" 
                                        @click="openNotes({{ $domain->id }}, {{ json_encode($domain->url) }})" 
                                        class="p-1.5 rounded-studio-sm bg-studio-elevated hover:bg-studio-border text-studio-muted hover:text-white transition-colors border border-studio-border" 
                                        title="Notizen">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <!-- Delete -->
                                <form method="POST" action="{{ route('domains.destroy', $domain) }}" id="delete-form-table-{{ $domain->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            @click="confirmDelete('domain', 'delete-form-table-{{ $domain->id }}', {{ json_encode($domain->url) }})"
                                            class="p-1.5 rounded-studio-sm bg-studio-elevated hover:bg-studio-rose/20 text-studio-muted hover:text-studio-rose transition-colors border border-studio-border" 
                                            title="Entfernen">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
