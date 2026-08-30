            <div class="space-y-6">

                <!-- 2. Response Time Chart -->
                <div class="spectora-card p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-studio-text">Antwortzeit Verlauf (Letzte 50 Checks)</h3>
                            <p class="text-[11px] text-studio-muted">Latenzmessung in Millisekunden</p>
                        </div>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="historyChart"></canvas>
                    </div>
                </div>

                <!-- 3. Probe Log Table -->
                <div class="spectora-card overflow-hidden">
                    <div class="p-4 border-b border-studio-border flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-studio-text">Letzte Probe-Checks (Logbuch)</h3>
                        <span class="text-[10px] text-studio-muted font-mono">Letzte 20 Einträge</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-studio-muted uppercase tracking-wider border-b border-studio-border bg-studio-elevated/50 text-[11px] font-bold">
                                    <th class="py-3 px-4">Zeitpunkt</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Antwortzeit</th>
                                    <th class="py-3 px-4">SSL Resttage</th>
                                    <th class="py-3 px-4">Watchdog</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-studio-border">
                                @forelse($recentChecks as $check)
                                    @php
                                        $checkTime = $check->checked_at 
                                            ? (\Carbon\Carbon::parse($check->checked_at)->format('d.m.Y H:i:s')) 
                                            : ($check->created_at ? $check->created_at->format('d.m.Y H:i:s') : '--');
                                        $isHealthy = $check->status_code >= 200 && $check->status_code < 400;
                                    @endphp
                                    <tr class="hover:bg-studio-elevated/60 transition-colors">
                                        <td class="py-3 px-4 text-studio-text font-mono text-[11px]">{{ $checkTime }}</td>
                                        <td class="py-3 px-4">
                                            @if($isHealthy)
                                                <span class="badge-status-online">● HTTP {{ $check->status_code }}</span>
                                            @else
                                                <span class="badge-status-offline">● {{ $check->status_code ? 'HTTP '.$check->status_code : 'Fehlgeschlagen' }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-studio-muted font-mono">
                                            @if(isset($check->response_time))
                                                <span class="text-studio-text font-bold">{{ round($check->response_time * 1000) }}</span> ms
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-mono text-[11px] text-studio-muted">
                                            {{ $check->ssl_days_left !== null ? $check->ssl_days_left.' d' : '--' }}
                                        </td>
                                        <td class="py-3 px-4 font-mono text-[11px]">
                                            <span class="{{ ($check->safety_status ?? 'safe') === 'safe' ? 'text-studio-emerald' : 'text-studio-rose' }}">
                                                {{ ($check->safety_status ?? 'safe') === 'safe' ? '✓ Sicher' : '⚠️ Alert' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-xs text-studio-muted">
                                            Noch keine Probe-Einträge vorhanden.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($recentChecks->hasPages())
                        <div class="p-3 border-t border-studio-border bg-studio-elevated/30">
                            {{ $recentChecks->links() }}
                        </div>
                    @endif
                </div>

            </div>
