            <div class="space-y-6">

                <!-- Row 1: 4 Spectora Studio Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Performance Card -->
                    <div class="spectora-card p-4.5">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-[11px] font-bold text-studio-muted uppercase tracking-wider">Performance Score</h3>
                            <span class="text-[10px] px-2 py-0.5 rounded font-mono font-bold {{ $score >= 90 ? 'bg-studio-emerald/15 text-studio-emerald border border-studio-emerald/30' : ($score >= 50 ? 'bg-studio-amber/15 text-studio-amber border border-studio-amber/30' : 'bg-studio-rose/15 text-studio-rose border border-studio-rose/30') }}">
                                {{ $score >= 90 ? 'Ausgezeichnet' : ($score >= 50 ? 'Durchschnitt' : 'Kritisch') }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-2">
                            <span class="text-2xl font-bold text-white font-mono">{{ $score }}</span>
                            <span class="text-xs text-studio-muted font-mono">/100</span>
                        </div>
                        <div class="border-t border-studio-border pt-2">
                            <div class="h-12">
                                <canvas id="performanceSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[10px] text-studio-subtle font-mono mt-1">
                                <span>7 Tage</span>
                                <span>Heute</span>
                            </div>
                        </div>
                    </div>

                    <!-- Uptime Card -->
                    <div class="spectora-card p-4.5">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-[11px] font-bold text-studio-muted uppercase tracking-wider">Uptime (30d)</h3>
                            <span class="text-[10px] px-2 py-0.5 rounded font-mono font-bold bg-studio-emerald/15 text-studio-emerald border border-studio-emerald/30">30 Tage</span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-2">
                            <span class="text-2xl font-bold font-mono {{ $uptime >= 99 ? 'text-studio-emerald' : 'text-studio-amber' }}">{{ number_format($uptime, 1) }}</span>
                            <span class="text-xs text-studio-muted font-mono">%</span>
                        </div>
                        <div class="border-t border-studio-border pt-2">
                            <div class="h-12">
                                <canvas id="uptimeSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[10px] text-studio-subtle font-mono mt-1">
                                <span>30 Tage</span>
                                <span>Heute</span>
                            </div>
                        </div>
                    </div>

                    <!-- Response Time Card -->
                    <div class="spectora-card p-4.5">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-[11px] font-bold text-studio-muted uppercase tracking-wider">Antwortzeit</h3>
                            <span class="text-[10px] px-2 py-0.5 rounded font-mono font-bold {{ $avgResponseTime < 300 ? 'bg-studio-brand/15 text-studio-brand-hover border border-studio-brand/30' : 'bg-studio-amber/15 text-studio-amber border border-studio-amber/30' }}">
                                {{ $avgResponseTime < 300 ? 'Schnell' : 'Akzeptabel' }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-2">
                            <span class="text-2xl font-bold text-white font-mono">
                                @if(isset($avgResponseTime))
                                    @if($avgResponseTime < 1000)
                                        {{ $avgResponseTime }}<span class="text-xs text-studio-muted font-mono">ms</span>
                                    @else
                                        {{ number_format($avgResponseTime / 1000, 2) }}<span class="text-xs text-studio-muted font-mono">s</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="border-t border-studio-border pt-2">
                            <div class="h-12">
                                <canvas id="responseSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[10px] text-studio-subtle font-mono mt-1">
                                <span>7 Tage</span>
                                <span>Heute</span>
                            </div>
                        </div>
                    </div>

                    <!-- SSL Certificate Card -->
                    <div class="spectora-card p-4.5">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-[11px] font-bold text-studio-muted uppercase tracking-wider">SSL Zertifikat</h3>
                                <svg class="w-3.5 h-3.5 {{ $sslDaysRemaining > 30 ? 'text-studio-emerald' : ($sslDaysRemaining > 7 ? 'text-studio-amber' : 'text-studio-rose') }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex items-baseline gap-1 mb-2">
                                <span class="text-2xl font-bold text-white font-mono">{{ $sslDaysRemaining }}</span>
                                <span class="text-xs text-studio-muted font-mono">Tage</span>
                            </div>
                        </div>
                        <div class="border-t border-studio-border pt-2">
                            <div class="relative pt-1">
                                <div class="overflow-hidden h-1.5 text-xs flex rounded-full bg-studio-bg">
                                    <div style="width:{{ min(100, ($sslDaysRemaining / 90) * 100) }}%" 
                                         class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $sslDaysRemaining > 60 ? 'bg-studio-emerald' : ($sslDaysRemaining > 30 ? 'bg-studio-emerald/80' : ($sslDaysRemaining > 7 ? 'bg-studio-amber' : 'bg-studio-rose')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between text-[10px] text-studio-subtle font-mono mt-2">
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
                            <span class="text-[10px] text-studio-muted font-mono">30 Tage</span>
                        </div>
                        <div class="flex items-center justify-center py-2">
                            <div class="relative w-40 h-40">
                                <canvas id="overviewDeviceChart"></canvas>
                            </div>
                        </div>
                        <!-- Legend -->
                        <div class="mt-3 space-y-2 border-t border-studio-border pt-3">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-studio-brand"></div>
                                    <span class="text-studio-muted">Desktop</span>
                                </div>
                                <span class="font-mono font-bold text-white">{{ $deviceStats['desktop'] ?? 0 }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-studio-emerald"></div>
                                    <span class="text-studio-muted">Mobile</span>
                                </div>
                                <span class="font-mono font-bold text-white">{{ $deviceStats['mobile'] ?? 0 }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-studio-amber"></div>
                                    <span class="text-studio-muted">Tablet</span>
                                </div>
                                <span class="font-mono font-bold text-white">{{ $deviceStats['tablet'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Traffic Line Chart (Right 2 Cols) -->
                    <div class="lg:col-span-2 spectora-card p-5">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-white">Traffic Verlauf (30 Tage)</h3>
                            <div class="flex items-center gap-4 text-xs font-mono">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-studio-brand"></div>
                                    <span class="text-studio-muted">Besucher:</span>
                                    <span class="font-bold text-white">{{ number_format(array_sum($chartVisitors ?? [])) }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-studio-emerald"></div>
                                    <span class="text-studio-muted">Views:</span>
                                    <span class="font-bold text-white">{{ number_format(array_sum($chartPageviews ?? [])) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="relative h-60 w-full">
                            <canvas id="overviewChart"></canvas>
                        </div>
                    </div>
                </div>

                <x-spectora.engine-report
                    :findings="$auditDetails ?? []"
                    :watchdog="$watchdogData ?? []"
                    :score="$score ?? null"
                    :grade="$domain->grade ?? null"
                />

            </div>
