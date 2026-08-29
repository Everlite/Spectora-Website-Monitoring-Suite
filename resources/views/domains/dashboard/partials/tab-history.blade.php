                    <!-- Tab Content: History -->
                    <div x-show="tab === 'history'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6"
                         x-data="historyManager()">
                        <div class="space-y-6">
                            <!-- Spectora Analysis Section -->
                            <div class="horizon-card p-6">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                                    <div>
                                        <h3 class="text-sm font-bold text-white">Spectora Tiefen-Analyse</h3>
                                        <p class="text-xs text-[#A3AED0]">Performance, Core Web Vitals & SEO-Bericht</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if($domain->updated_at)
                                            <span class="text-xs text-[#A3AED0]">
                                                Letzter Scan: {{ $domain->updated_at->diffForHumans() }}
                                            </span>
                                        @endif
                                        
                                        <button 
                                            @click="runAnalysis()"
                                            :disabled="isAnalyzing"
                                            class="btn-horizon-primary"
                                        >
                                            <template x-if="isAnalyzing">
                                                <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </template>
                                            <span x-text="isAnalyzing ? 'Prüfung läuft...' : 'Jetzt analysieren'"></span>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Analysis Feedback -->
                                <div x-show="analysisResult" x-cloak class="mb-6">
                                    <div x-show="analysisResult === 'success'" class="bg-[#01B574]/20 border border-[#01B574]/40 rounded-horizon-sm p-4 flex items-center gap-3">
                                        <svg class="w-5 h-5 text-[#01B574] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <div>
                                            <p class="text-[#01B574] font-bold text-xs">Analyse erfolgreich abgeschlossen!</p>
                                            <p class="text-[#A3AED0] text-[11px]">Seite wird aktualisiert...</p>
                                        </div>
                                    </div>
                                    <div x-show="analysisResult === 'error'" class="bg-[#EE5D50]/20 border border-[#EE5D50]/40 rounded-horizon-sm p-4 flex items-center gap-3">
                                        <svg class="w-5 h-5 text-[#EE5D50] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <div>
                                            <p class="text-[#EE5D50] font-bold text-xs">Analyse fehlgeschlagen</p>
                                            <p class="text-[#A3AED0] text-[11px]" x-text="analysisError"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Details List -->
                                @if ($domain->last_pagespeed_details)
                                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                                        @foreach ($domain->last_pagespeed_details as $item)
                                            <div class="bg-[#0B1437] border border-[#1B254B] rounded-horizon-sm p-4 flex items-start gap-4">
                                                <div class="flex-shrink-0 mt-0.5">
                                                    @php $status = $item['status'] ?? 'unknown'; @endphp
                                                    @if ($status === 'success')
                                                        <svg class="w-4 h-4 text-[#01B574]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @elseif($status === 'warning')
                                                        <svg class="w-4 h-4 text-[#FFB547]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-[#EE5D50]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h4 class="font-bold text-white text-xs">{{ $item['label'] ?? 'Prüfpunkt' }}</h4>
                                                    <p class="text-xs text-[#A3AED0] mt-0.5">{{ $item['message'] ?? '' }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <p class="text-[#A3AED0] text-xs">Noch keine Tiefenprüfung durchgeführt.</p>
                                        <p class="text-white text-xs font-semibold mt-1">Klicke oben auf "Jetzt analysieren".</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Check History / Events Log -->
                            <div class="horizon-card p-6">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                    <div>
                                        <h3 class="text-sm font-bold text-white">Ereignis-Protokoll</h3>
                                        <p class="text-xs text-[#A3AED0]">Letzte Uptime-Probes & Statuswechsel</p>
                                    </div>
                                    
                                    <div class="flex items-center gap-3">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="showAllLogs" class="sr-only peer">
                                            <div class="relative w-9 h-5 bg-[#0B1437] border border-[#1B254B] rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-[#A3AED0] after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#7551FF] peer-checked:after:bg-white"></div>
                                            <span class="text-xs text-[#A3AED0]">Alle anzeigen</span>
                                        </label>
                                    </div>
                                </div>
                                
                                @php
                                    $issueChecks = $recentChecks->filter(function($check) {
                                        return $check->status_code >= 400 || $check->status_code === 0 || $check->status_code === null;
                                    });
                                @endphp
                                
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead>
                                            <tr class="text-[#A3AED0] uppercase tracking-wider border-b border-[#1B254B] text-[11px] font-bold">
                                                <th class="pb-3">Zeitpunkt</th>
                                                <th class="pb-3">Status</th>
                                                <th class="pb-3">Latenz</th>
                                                <th class="pb-3">Details</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#1B254B]/60">
                                            @foreach($recentChecks->take(15) as $check)
                                                @php $isSuccess = $check->status_code >= 200 && $check->status_code < 400; @endphp
                                                <tr class="hover:bg-[#121E4A] transition-colors" x-show="showAllLogs || {{ $isSuccess ? 'false' : 'true' }}">
                                                    <td class="py-3 text-white font-mono">{{ $check->checked_at ? $check->checked_at->format('d.m.Y H:i:s') : $check->created_at->format('d.m.Y H:i:s') }}</td>
                                                    <td class="py-3">
                                                        @if($isSuccess)
                                                            <span class="badge-horizon-success text-[10px]">HTTP {{ $check->status_code }}</span>
                                                        @else
                                                            <span class="badge-horizon-danger text-[10px]">HTTP {{ $check->status_code ?: 'Error' }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 font-mono text-[#A3AED0]">{{ $check->response_time ? round($check->response_time * 1000) . ' ms' : '--' }}</td>
                                                    <td class="py-3 text-[#A3AED0] truncate max-w-xs">{{ $check->error_message ?: 'OK' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
