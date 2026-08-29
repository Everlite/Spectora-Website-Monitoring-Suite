@props(['domains'])

<div class="horizon-card p-6 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-[#1B254B] text-[11px] font-bold text-[#A3AED0] uppercase tracking-wider">
                    <th class="pb-3.5 px-3">Ziel-Website</th>
                    <th class="pb-3.5 px-3">Status</th>
                    <th class="pb-3.5 px-3">30d Uptime</th>
                    <th class="pb-3.5 px-3">Latenz</th>
                    <th class="pb-3.5 px-3">SSL Zertifikat</th>
                    <th class="pb-3.5 px-3">Besucher (24h)</th>
                    <th class="pb-3.5 px-3">Watchdog</th>
                    <th class="pb-3.5 px-3 text-right">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#1B254B]/60">
                @foreach ($domains as $domain)
                    @php
                        $isOnline = $domain->status_code >= 200 && $domain->status_code < 400;
                        $sslDays = $domain->ssl_days_left ?? 0;
                        $uptime = $domain->calculated_uptime ?? $domain->calculateUptime();
                    @endphp
                    <tr class="hover:bg-[#121E4A] transition-colors"
                        x-show="matchesFilter({{ json_encode(strtolower($domain->url)) }}, {{ json_encode($isOnline ? 'online' : 'offline') }})">
                        
                        <!-- Domain Target -->
                        <td class="py-4 px-3">
                            <div class="flex items-center gap-3">
                                <span class="relative flex h-2.5 w-2.5 shrink-0">
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $isOnline ? 'bg-[#01B574]' : 'bg-[#EE5D50]' }}"></span>
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('domains.show', $domain) }}" class="font-bold text-white hover:text-[#7551FF] transition-colors truncate block text-sm">
                                        {{ $domain->url }}
                                    </a>
                                    <span class="text-[10px] text-[#A3AED0]">
                                        Vor {{ $domain->last_checked ? $domain->last_checked->diffForHumans(null, true) : 'nie' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-4 px-3">
                            @if($isOnline)
                                <span class="badge-horizon-success">
                                    ● Online ({{ $domain->status_code }})
                                </span>
                            @else
                                <span class="badge-horizon-danger">
                                    ● {{ $domain->status_code ? 'HTTP '.$domain->status_code : 'Offline' }}
                                </span>
                            @endif
                        </td>

                        <!-- 30d Uptime -->
                        <td class="py-4 px-3 font-mono">
                            <span class="font-extrabold text-sm {{ $uptime >= 99 ? 'text-[#01B574]' : ($uptime >= 95 ? 'text-[#FFB547]' : 'text-[#EE5D50]') }}">
                                {{ number_format($uptime, 1) }}%
                            </span>
                        </td>

                        <!-- Latency -->
                        <td class="py-4 px-3 font-mono text-[#A3AED0]">
                            @if(isset($domain->response_time))
                                @if($domain->response_time < 1)
                                    <span class="text-white font-semibold">{{ round($domain->response_time * 1000) }}</span> ms
                                @else
                                    <span class="text-white font-semibold">{{ number_format($domain->response_time, 2) }}</span> s
                                @endif
                            @else
                                --
                            @endif
                        </td>

                        <!-- SSL Days -->
                        <td class="py-4 px-3 font-mono text-xs">
                            <span class="{{ $sslDays > 30 ? 'text-[#A3AED0]' : ($sslDays > 7 ? 'text-[#FFB547]' : 'text-[#EE5D50]') }}">
                                {{ $sslDays > 0 ? $sslDays.' Tage' : ($sslDays === 0 ? 'Läuft ab' : 'Kein SSL') }}
                            </span>
                        </td>

                        <!-- Visitors -->
                        <td class="py-4 px-3 font-mono font-bold text-white text-sm">
                            {{ number_format($domain->visitors_count_today ?? $domain->visitors_today ?? 0) }}
                        </td>

                        <!-- Watchdog -->
                        <td class="py-4 px-3">
                            <button type="button" 
                                    @click="openWatchdog({{ json_encode($domain->url) }}, {{ json_encode($domain->safety_details ?? []) }}, {{ json_encode($domain->safety_status) }})"
                                    class="cursor-pointer
                                    @if($domain->safety_status === 'safe') badge-horizon-neutral
                                    @elseif($domain->safety_status === 'danger') badge-horizon-danger
                                    @else badge-horizon-warning
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
                        <td class="py-4 px-3 text-right">
                            <div class="inline-flex items-center gap-1.5">
                                <!-- Tracking Code Button -->
                                <button type="button" 
                                        @click="openTracking({{ json_encode($domain->url) }}, {{ json_encode($domain->uuid) }})"
                                        class="p-2 rounded-full bg-[#1B254B] hover:bg-[#7551FF] text-[#A3AED0] hover:text-white transition-all shadow-sm"
                                        title="Pulse Tracking Code">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                                </button>

                                <!-- Details Link -->
                                <a href="{{ route('domains.show', $domain) }}" 
                                   class="px-3.5 py-1.5 rounded-full bg-[#1B254B] hover:bg-[#7551FF] text-white font-bold text-xs transition-all shadow-sm">
                                    Details
                                </a>

                                <!-- Notes Button -->
                                <button type="button" 
                                        @click="openNotes({{ $domain->id }}, {{ json_encode($domain->url) }})" 
                                        class="p-2 rounded-full bg-[#1B254B] hover:bg-[#2B3674] text-[#A3AED0] hover:text-white transition-all" 
                                        title="Notizen">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>

                                <!-- Delete -->
                                <form method="POST" action="{{ route('domains.destroy', $domain) }}" id="delete-form-table-{{ $domain->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" 
                                            @click="confirmDelete('domain', 'delete-form-table-{{ $domain->id }}', {{ json_encode($domain->url) }})"
                                            class="p-2 rounded-full bg-[#1B254B] hover:bg-[#EE5D50]/30 text-[#A3AED0] hover:text-[#EE5D50] transition-all" 
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
