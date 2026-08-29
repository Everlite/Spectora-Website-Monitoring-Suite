            <!-- Tab Content: Overview -->
            <div x-show="tab === 'overview'" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">

                <!-- Row 1: 4 Horizon Stat Cards -->
                <div class="custom-grid-4">
                    
                    <!-- Performance Card -->
                    <div class="horizon-card p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold text-[#A3AED0] uppercase tracking-wider">Performance Score</h3>
                            <span class="text-[10px] px-2.5 py-0.5 rounded-full font-mono font-bold {{ $score >= 90 ? 'bg-[#01B574]/20 text-[#01B574] border border-[#01B574]/40' : ($score >= 50 ? 'bg-[#FFB547]/20 text-[#FFB547] border border-[#FFB547]/40' : 'bg-[#EE5D50]/20 text-[#EE5D50] border border-[#EE5D50]/40') }}">
                                {{ $score >= 90 ? 'Ausgezeichnet' : ($score >= 50 ? 'Durchschnitt' : 'Kritisch') }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-3">
                            <span class="text-3xl font-extrabold text-white font-mono">{{ $score }}</span>
                            <span class="text-xs text-[#A3AED0] font-mono">/100</span>
                        </div>
                        <div class="border-t border-[#1B254B] pt-2">
                            <div class="h-12 lg:h-14">
                                <canvas id="performanceSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[10px] text-[#A3AED0] font-mono mt-1">
                                <span>7 Tage</span>
                                <span>Heute</span>
                            </div>
                        </div>
                    </div>

                    <!-- Uptime Card -->
                    <div class="horizon-card p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold text-[#A3AED0] uppercase tracking-wider">Uptime (30d)</h3>
                            <span class="text-[10px] px-2.5 py-0.5 rounded-full font-mono font-bold bg-[#01B574]/20 text-[#01B574] border border-[#01B574]/40">30 Tage</span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-3">
                            <span class="text-3xl font-extrabold text-white font-mono">{{ number_format($uptime, 1) }}</span>
                            <span class="text-xs text-[#A3AED0] font-mono">%</span>
                        </div>
                        <div class="border-t border-[#1B254B] pt-2">
                            <div class="h-12 lg:h-14">
                                <canvas id="uptimeSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[10px] text-[#A3AED0] font-mono mt-1">
                                <span>30 Tage</span>
                                <span>Heute</span>
                            </div>
                        </div>
                    </div>

                    <!-- Response Time Card -->
                    <div class="horizon-card p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold text-[#A3AED0] uppercase tracking-wider">Antwortzeit</h3>
                            <span class="text-[10px] px-2.5 py-0.5 rounded-full font-mono font-bold {{ $avgResponseTime < 300 ? 'bg-[#7551FF]/20 text-[#7551FF] border border-[#7551FF]/40' : 'bg-[#FFB547]/20 text-[#FFB547] border border-[#FFB547]/40' }}">
                                {{ $avgResponseTime < 300 ? 'Schnell' : 'Akzeptabel' }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-3">
                            <span class="text-3xl font-extrabold text-white font-mono">
                                @if(isset($avgResponseTime))
                                    @if($avgResponseTime < 1000)
                                        {{ $avgResponseTime }}<span class="text-xs text-[#A3AED0] font-mono">ms</span>
                                    @else
                                        {{ number_format($avgResponseTime / 1000, 2) }}<span class="text-xs text-[#A3AED0] font-mono">s</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="border-t border-[#1B254B] pt-2">
                            <div class="h-12 lg:h-14">
                                <canvas id="responseSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[10px] text-[#A3AED0] font-mono mt-1">
                                <span>7 Tage</span>
                                <span>Heute</span>
                            </div>
                        </div>
                    </div>

                    <!-- SSL Certificate Card -->
                    <div class="horizon-card p-5">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xs font-bold text-[#A3AED0] uppercase tracking-wider">SSL Zertifikat</h3>
                                <svg class="w-4 h-4 {{ $sslDaysRemaining > 30 ? 'text-[#01B574]' : ($sslDaysRemaining > 7 ? 'text-[#FFB547]' : 'text-[#EE5D50]') }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex items-baseline gap-1 mb-3">
                                <span class="text-3xl font-extrabold text-white font-mono">{{ $sslDaysRemaining }}</span>
                                <span class="text-xs text-[#A3AED0] font-mono">Tage</span>
                            </div>
                        </div>
                        <div class="border-t border-[#1B254B] pt-2">
                            <div class="relative pt-1">
                                <div class="overflow-hidden h-2 text-xs flex rounded-full bg-[#0B1437]">
                                    <div style="width:{{ min(100, ($sslDaysRemaining / 90) * 100) }}%" 
                                         class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $sslDaysRemaining > 60 ? 'bg-[#01B574]' : ($sslDaysRemaining > 30 ? 'bg-[#05CD99]' : ($sslDaysRemaining > 7 ? 'bg-[#FFB547]' : 'bg-[#EE5D50]')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between text-[10px] text-[#A3AED0] font-mono mt-2">
                                <span>0 Tage</span>
                                <span>90 Tage</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Geräte + Traffic (Horizon style) -->
                <div class="custom-grid-2">
                    
                    <!-- Device Pie Chart (Left) -->
                    <div class="min-w-0 horizon-card p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Geräteverteilung</h3>
                            <span class="text-[10px] text-[#A3AED0] font-mono">Letzte 30 Tage</span>
                        </div>
                        <div class="flex items-center justify-center">
                            <div class="relative w-44 h-44 max-w-full">
                                <canvas id="overviewDeviceChart"></canvas>
                            </div>
                        </div>
                        <!-- Legend -->
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#7551FF]"></div>
                                    <span class="text-[#A3AED0]">Desktop</span>
                                </div>
                                <span class="font-mono font-bold text-white">{{ $deviceStats['desktop'] ?? 0 }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#01B574]"></div>
                                    <span class="text-[#A3AED0]">Mobile</span>
                                </div>
                                <span class="font-mono font-bold text-white">{{ $deviceStats['mobile'] ?? 0 }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#FFB547]"></div>
                                    <span class="text-[#A3AED0]">Tablet</span>
                                </div>
                                <span class="font-mono font-bold text-white">{{ $deviceStats['tablet'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Traffic Line Chart (Right) -->
                    <div class="min-w-0 horizon-card p-6 cursor-pointer hover:border-[#7551FF]/60 transition-colors"
                         @click="tab = 'analytics'" role="button" tabindex="0" @keydown.enter="tab = 'analytics'">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Traffic Übersicht</h3>
                            <div class="flex items-center gap-4 text-xs font-mono">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-[#7551FF]"></div>
                                    <span class="text-[#A3AED0]">Besucher:</span>
                                    <span class="font-bold text-white">{{ number_format(array_sum($chartVisitors)) }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-[#01B574]"></div>
                                    <span class="text-[#A3AED0]">Views:</span>
                                    <span class="font-bold text-white">{{ number_format(array_sum($chartPageviews)) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="relative h-60 w-full">
                            <canvas id="overviewChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Security Alert (nur wenn Issues) -->
                @if($criticalCount + $warningCount > 0)
                <div @click="showSecurityModal = true"
                     class="horizon-card p-4 flex items-center gap-3 cursor-pointer bg-[#EE5D50]/10 border-[#EE5D50]/30 hover:bg-[#EE5D50]/20 transition-all group">
                    <svg class="w-6 h-6 text-[#EE5D50] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div class="flex-1">
                        <span class="font-bold text-white">{{ $criticalCount + $warningCount }} Sicherheits-Probleme gefunden</span>
                        <span class="text-xs ml-2 text-[#A3AED0] group-hover:underline">→ Für Details klicken</span>
                    </div>
                    <svg class="w-5 h-5 text-[#A3AED0] group-hover:text-white transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
                @endif

            </div>
