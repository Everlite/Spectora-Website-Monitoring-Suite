            <!-- Tab Content: Overview -->
            <div x-show="tab === 'overview'" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">

                <!-- Row 1: 4 Spectora Studio Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Performance Card -->
                    <div class="spectora-card p-4.5">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-[11px] font-bold text-[#8A95A8] uppercase tracking-wider">Performance Score</h3>
                            <span class="text-[10px] px-2 py-0.5 rounded font-mono font-bold {{ $score >= 90 ? 'bg-[#10B981]/15 text-[#10B981] border border-[#10B981]/30' : ($score >= 50 ? 'bg-[#F59E0B]/15 text-[#F59E0B] border border-[#F59E0B]/30' : 'bg-[#F43F5E]/15 text-[#F43F5E] border border-[#F43F5E]/30') }}">
                                {{ $score >= 90 ? 'Ausgezeichnet' : ($score >= 50 ? 'Durchschnitt' : 'Kritisch') }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-2">
                            <span class="text-2xl font-bold text-white font-mono">{{ $score }}</span>
                            <span class="text-xs text-[#8A95A8] font-mono">/100</span>
                        </div>
                        <div class="border-t border-[#202A3E] pt-2">
                            <div class="h-12">
                                <canvas id="performanceSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[10px] text-[#5A667A] font-mono mt-1">
                                <span>7 Tage</span>
                                <span>Heute</span>
                            </div>
                        </div>
                    </div>

                    <!-- Uptime Card -->
                    <div class="spectora-card p-4.5">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-[11px] font-bold text-[#8A95A8] uppercase tracking-wider">Uptime (30d)</h3>
                            <span class="text-[10px] px-2 py-0.5 rounded font-mono font-bold bg-[#10B981]/15 text-[#10B981] border border-[#10B981]/30">30 Tage</span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-2">
                            <span class="text-2xl font-bold font-mono {{ $uptime >= 99 ? 'text-[#10B981]' : 'text-[#F59E0B]' }}">{{ number_format($uptime, 1) }}</span>
                            <span class="text-xs text-[#8A95A8] font-mono">%</span>
                        </div>
                        <div class="border-t border-[#202A3E] pt-2">
                            <div class="h-12">
                                <canvas id="uptimeSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[10px] text-[#5A667A] font-mono mt-1">
                                <span>30 Tage</span>
                                <span>Heute</span>
                            </div>
                        </div>
                    </div>

                    <!-- Response Time Card -->
                    <div class="spectora-card p-4.5">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-[11px] font-bold text-[#8A95A8] uppercase tracking-wider">Antwortzeit</h3>
                            <span class="text-[10px] px-2 py-0.5 rounded font-mono font-bold {{ $avgResponseTime < 300 ? 'bg-[#3B57E8]/15 text-[#4F6BFF] border border-[#3B57E8]/30' : 'bg-[#F59E0B]/15 text-[#F59E0B] border border-[#F59E0B]/30' }}">
                                {{ $avgResponseTime < 300 ? 'Schnell' : 'Akzeptabel' }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-2">
                            <span class="text-2xl font-bold text-white font-mono">
                                @if(isset($avgResponseTime))
                                    @if($avgResponseTime < 1000)
                                        {{ $avgResponseTime }}<span class="text-xs text-[#8A95A8] font-mono">ms</span>
                                    @else
                                        {{ number_format($avgResponseTime / 1000, 2) }}<span class="text-xs text-[#8A95A8] font-mono">s</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="border-t border-[#202A3E] pt-2">
                            <div class="h-12">
                                <canvas id="responseSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[10px] text-[#5A667A] font-mono mt-1">
                                <span>7 Tage</span>
                                <span>Heute</span>
                            </div>
                        </div>
                    </div>

                    <!-- SSL Certificate Card -->
                    <div class="spectora-card p-4.5">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-[11px] font-bold text-[#8A95A8] uppercase tracking-wider">SSL Zertifikat</h3>
                                <svg class="w-3.5 h-3.5 {{ $sslDaysRemaining > 30 ? 'text-[#10B981]' : ($sslDaysRemaining > 7 ? 'text-[#F59E0B]' : 'text-[#F43F5E]') }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex items-baseline gap-1 mb-2">
                                <span class="text-2xl font-bold text-white font-mono">{{ $sslDaysRemaining }}</span>
                                <span class="text-xs text-[#8A95A8] font-mono">Tage</span>
                            </div>
                        </div>
                        <div class="border-t border-[#202A3E] pt-2">
                            <div class="relative pt-1">
                                <div class="overflow-hidden h-1.5 text-xs flex rounded-full bg-[#090B10]">
                                    <div style="width:{{ min(100, ($sslDaysRemaining / 90) * 100) }}%" 
                                         class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $sslDaysRemaining > 60 ? 'bg-[#10B981]' : ($sslDaysRemaining > 30 ? 'bg-[#10B981]/80' : ($sslDaysRemaining > 7 ? 'bg-[#F59E0B]' : 'bg-[#F43F5E]')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between text-[10px] text-[#5A667A] font-mono mt-2">
                                <span>0 Tage</span>
                                <span>90 Tage</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Geräte + Traffic (Studio style) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    
                    <!-- Device Pie Chart (Left 1 Col) -->
                    <div class="spectora-card p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Geräteverteilung</h3>
                            <span class="text-[10px] text-[#8A95A8] font-mono">30 Tage</span>
                        </div>
                        <div class="flex items-center justify-center py-2">
                            <div class="relative w-40 h-40">
                                <canvas id="overviewDeviceChart"></canvas>
                            </div>
                        </div>
                        <!-- Legend -->
                        <div class="mt-3 space-y-2 border-t border-[#202A3E] pt-3">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#3B57E8]"></div>
                                    <span class="text-[#8A95A8]">Desktop</span>
                                </div>
                                <span class="font-mono font-bold text-white">{{ $deviceStats['desktop'] ?? 0 }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></div>
                                    <span class="text-[#8A95A8]">Mobile</span>
                                </div>
                                <span class="font-mono font-bold text-white">{{ $deviceStats['mobile'] ?? 0 }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></div>
                                    <span class="text-[#8A95A8]">Tablet</span>
                                </div>
                                <span class="font-mono font-bold text-white">{{ $deviceStats['tablet'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Traffic Line Chart (Right 2 Cols) -->
                    <div class="lg:col-span-2 spectora-card p-5 cursor-pointer hover:border-[#3B57E8]/60 transition-colors"
                         @click="tab = 'analytics'" role="button" tabindex="0" @keydown.enter="tab = 'analytics'">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Traffic Verlauf (30 Tage)</h3>
                            <div class="flex items-center gap-4 text-xs font-mono">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-[#3B57E8]"></div>
                                    <span class="text-[#8A95A8]">Besucher:</span>
                                    <span class="font-bold text-white">{{ number_format(array_sum($chartVisitors ?? [])) }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-[#10B981]"></div>
                                    <span class="text-[#8A95A8]">Views:</span>
                                    <span class="font-bold text-white">{{ number_format(array_sum($chartPageviews ?? [])) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="relative h-60 w-full">
                            <canvas id="overviewChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Security Alert (nur wenn Issues vorhanden sind) -->
                @if(($criticalCount ?? 0) + ($warningCount ?? 0) > 0)
                <div @click="showSecurityModal = true"
                     class="spectora-card p-4 flex items-center gap-3 cursor-pointer bg-[#F43F5E]/10 border-[#F43F5E]/30 hover:bg-[#F43F5E]/15 transition-all group">
                    <div class="w-8 h-8 rounded-studio-sm bg-[#F43F5E]/20 text-[#F43F5E] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <span class="font-bold text-white text-xs">{{ ($criticalCount ?? 0) + ($warningCount ?? 0) }} Sicherheits-Hinweise gefunden</span>
                        <span class="text-xs ml-2 text-[#8A95A8] group-hover:underline">→ Für Details klicken</span>
                    </div>
                    <svg class="w-4 h-4 text-[#8A95A8] group-hover:text-white transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
                @endif

            </div>
