@props(['domains'])

<div class="rounded-xl border border-border bg-card overflow-hidden shadow-shadcn-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-border bg-muted/40 text-muted-foreground font-medium">
                    <th class="py-3 px-4">Ziel-Website</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">30d Uptime</th>
                    <th class="py-3 px-4">Latenz</th>
                    <th class="py-3 px-4">SSL Zertifikat</th>
                    <th class="py-3 px-4">Besucher (24h)</th>
                    <th class="py-3 px-4">Watchdog</th>
                    <th class="py-3 px-4 text-right">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ($domains as $domain)
                    @php
                        $isOnline = $domain->status_code >= 200 && $domain->status_code < 400;
                        $sslDays = $domain->ssl_days_left ?? 0;
                        $uptime = $domain->calculated_uptime ?? $domain->calculateUptime();
                    @endphp
                    <tr class="hover:bg-muted/30 transition-colors"
                        x-show="matchesFilter({{ json_encode(strtolower($domain->url)) }}, {{ json_encode($isOnline ? 'online' : 'offline') }})">
                        
                        <!-- Domain Target -->
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2.5">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $isOnline ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('domains.show', $domain) }}" class="font-semibold text-foreground hover:underline truncate block">
                                        {{ $domain->url }}
                                    </a>
                                    <span class="text-[10px] text-muted-foreground">
                                        Vor {{ $domain->last_checked ? $domain->last_checked->diffForHumans(null, true) : 'nie' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3.5 px-4">
                            @if($isOnline)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-950/40 text-emerald-400 border border-emerald-800/40">
                                    Online ({{ $domain->status_code }})
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-rose-950/40 text-rose-400 border border-rose-800/40">
                                    {{ $domain->status_code ? 'HTTP '.$domain->status_code : 'Offline' }}
                                </span>
                            @endif
                        </td>

                        <!-- 30d Uptime -->
                        <td class="py-3.5 px-4 font-mono">
                            <span class="font-semibold {{ $uptime >= 99 ? 'text-emerald-400' : ($uptime >= 95 ? 'text-amber-400' : 'text-rose-400') }}">
                                {{ number_format($uptime, 1) }}%
                            </span>
                        </td>

                        <!-- Latency -->
                        <td class="py-3.5 px-4 font-mono text-muted-foreground">
                            @if(isset($domain->response_time))
                                @if($domain->response_time < 1)
                                    {{ round($domain->response_time * 1000) }} ms
                                @else
                                    {{ number_format($domain->response_time, 2) }} s
                                @endif
                            @else
                                --
                            @endif
                        </td>

                        <!-- SSL Days -->
                        <td class="py-3.5 px-4 font-mono text-[11px]">
                            <span class="{{ $sslDays > 30 ? 'text-muted-foreground' : ($sslDays > 7 ? 'text-amber-400' : 'text-rose-400') }}">
                                {{ $sslDays > 0 ? $sslDays.' d' : ($sslDays === 0 ? 'Expiring' : 'Kein SSL') }}
                            </span>
                        </td>

                        <!-- Visitors -->
                        <td class="py-3.5 px-4 font-mono font-semibold text-foreground">
                            {{ number_format($domain->visitors_count_today ?? $domain->visitors_today ?? 0) }}
                        </td>

                        <!-- Watchdog -->
                        <td class="py-3.5 px-4">
                            <button type="button" 
                                    @click="openWatchdog({{ json_encode($domain->url) }}, {{ json_encode($domain->safety_details ?? []) }}, {{ json_encode($domain->safety_status) }})"
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-medium
                                    @if($domain->safety_status === 'safe') bg-zinc-800 text-zinc-300 border border-zinc-700
                                    @elseif($domain->safety_status === 'danger') bg-rose-950/60 text-rose-400 border border-rose-800/40
                                    @else bg-amber-950/60 text-amber-400 border border-amber-800/40
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
                                        class="p-1.5 rounded-md hover:bg-secondary text-muted-foreground hover:text-foreground border border-border transition-colors"
                                        title="Pulse Tracking Code">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                </button>

                                <!-- Details Link -->
                                <a href="{{ route('domains.show', $domain) }}" 
                                   class="px-2.5 py-1 rounded-md bg-secondary hover:bg-accent text-foreground font-medium text-xs border border-border transition-colors">
                                    Details
                                </a>

                                <!-- Notes Button -->
                                <button type="button" 
                                        @click="openNotes({{ $domain->id }}, {{ json_encode($domain->url) }})" 
                                        class="p-1.5 rounded-md hover:bg-secondary text-muted-foreground hover:text-foreground border border-border transition-colors" 
                                        title="Notizen">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <!-- Delete -->
                                <form method="POST" action="{{ route('domains.destroy', $domain) }}" id="delete-form-table-{{ $domain->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            @click="confirmDelete('domain', 'delete-form-table-{{ $domain->id }}', {{ json_encode($domain->url) }})"
                                            class="p-1.5 rounded-md hover:bg-rose-950/40 text-muted-foreground hover:text-rose-400 border border-border transition-colors" 
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
