            <!-- Tab Content: History & Checks -->
            <div x-show="tab === 'history'" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">

                <!-- 1. Audit & Safety Details -->
                @if(!empty($auditDetails) || !empty($watchdogData))
                <div class="spectora-card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-studio-sm bg-[#171E2E] border border-[#202A3E] flex items-center justify-center text-[#3B57E8]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-white">Spectora Audit &amp; Watchdog Bericht</h3>
                                <p class="text-[11px] text-[#8A95A8]">Letzter automatischer Sicherheits- und Performance-Scan</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold font-mono {{ ($criticalCount ?? 0) === 0 ? 'bg-[#10B981]/15 text-[#10B981] border border-[#10B981]/30' : 'bg-[#F43F5E]/15 text-[#F43F5E] border border-[#F43F5E]/30' }}">
                                {{ ($criticalCount ?? 0) === 0 ? '0 Kritische Fehler' : $criticalCount.' Kritisch' }}
                            </span>
                            @if(($warningCount ?? 0) > 0)
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold font-mono bg-[#F59E0B]/15 text-[#F59E0B] border border-[#F59E0B]/30">
                                    {{ $warningCount }} Warnungen
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Items Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($auditDetails as $key => $detail)
                            @php
                                $status = is_array($detail) ? ($detail['status'] ?? 'ok') : 'ok';
                                $scoreVal = is_array($detail) ? ($detail['score'] ?? null) : null;
                                $message = is_array($detail) ? ($detail['message'] ?? '') : (string)$detail;
                            @endphp
                            <div class="p-3 rounded-studio-sm border border-[#202A3E] bg-[#090B10] flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5
                                    @if($status === 'ok' || $status === 'passed') bg-[#10B981]/15 text-[#10B981]
                                    @elseif($status === 'warning') bg-[#F59E0B]/15 text-[#F59E0B]
                                    @else bg-[#F43F5E]/15 text-[#F43F5E]
                                    @endif">
                                    @if($status === 'ok' || $status === 'passed')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    @elseif($status === 'warning')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-xs font-bold text-white uppercase tracking-wider font-mono">{{ ucwords(str_replace('_', ' ', $key)) }}</h4>
                                        @if($scoreVal !== null)
                                            <span class="text-[10px] font-mono font-bold text-[#8A95A8]">{{ $scoreVal }}/100</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#8A95A8] mt-0.5">{{ $message }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- 2. Response Time Chart -->
                <div class="spectora-card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Antwortzeit Verlauf (Letzte 50 Checks)</h3>
                            <p class="text-[11px] text-[#8A95A8]">Latenzmessung in Millisekunden</p>
                        </div>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="historyChart"></canvas>
                    </div>
                </div>

                <!-- 3. Probe Log Table -->
                <div class="spectora-card overflow-hidden">
                    <div class="p-4 border-b border-[#202A3E] flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-white">Letzte Probe-Checks (Logbuch)</h3>
                        <span class="text-[10px] text-[#8A95A8] font-mono">Letzte 20 Einträge</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[#8A95A8] uppercase tracking-wider border-b border-[#202A3E] bg-[#171E2E]/50 text-[11px] font-bold">
                                    <th class="py-3 px-4">Zeitpunkt</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Antwortzeit</th>
                                    <th class="py-3 px-4">SSL Resttage</th>
                                    <th class="py-3 px-4">Watchdog</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#202A3E]">
                                @forelse($recentChecks as $check)
                                    @php
                                        $checkTime = $check->checked_at 
                                            ? (\Carbon\Carbon::parse($check->checked_at)->format('d.m.Y H:i:s')) 
                                            : ($check->created_at ? $check->created_at->format('d.m.Y H:i:s') : '--');
                                        $isHealthy = $check->status_code >= 200 && $check->status_code < 400;
                                    @endphp
                                    <tr class="hover:bg-[#171E2E]/60 transition-colors">
                                        <td class="py-3 px-4 text-white font-mono text-[11px]">{{ $checkTime }}</td>
                                        <td class="py-3 px-4">
                                            @if($isHealthy)
                                                <span class="badge-status-online">● HTTP {{ $check->status_code }}</span>
                                            @else
                                                <span class="badge-status-offline">● {{ $check->status_code ? 'HTTP '.$check->status_code : 'Fehlgeschlagen' }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-[#8A95A8] font-mono">
                                            @if(isset($check->response_time))
                                                <span class="text-white font-bold">{{ round($check->response_time * 1000) }}</span> ms
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-mono text-[11px] text-[#8A95A8]">
                                            {{ $check->ssl_days_left !== null ? $check->ssl_days_left.' d' : '--' }}
                                        </td>
                                        <td class="py-3 px-4 font-mono text-[11px]">
                                            <span class="{{ ($check->safety_status ?? 'safe') === 'safe' ? 'text-[#10B981]' : 'text-[#F43F5E]' }}">
                                                {{ ($check->safety_status ?? 'safe') === 'safe' ? '✓ Sicher' : '⚠️ Alert' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-xs text-[#8A95A8]">
                                            Noch keine Probe-Einträge vorhanden.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($recentChecks->hasPages())
                        <div class="p-3 border-t border-[#202A3E] bg-[#171E2E]/30">
                            {{ $recentChecks->links() }}
                        </div>
                    @endif
                </div>

            </div>
