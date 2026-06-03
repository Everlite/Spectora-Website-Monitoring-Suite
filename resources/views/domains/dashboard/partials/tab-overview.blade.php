            <!-- Tab Content: Overview -->
            <div x-show="tab === 'overview'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">

                @php
                    $score = $domain->pagespeed_score_desktop ?? 0;
                    $scoreColor = $score >= 90 ? 'green' : ($score >= 50 ? 'orange' : 'red');
                    $watchdogData = $domain->safety_details['watchdog'] ?? null;
                    $securityIssues = $watchdogData['issues'] ?? [];
                    $criticalCount = count(array_filter($securityIssues, fn($i) => ($i['severity'] ?? '') === 'critical'));
                    $warningCount = count(array_filter($securityIssues, fn($i) => ($i['severity'] ?? '') === 'warning'));
                    $auditDetails = $domain->last_pagespeed_details ?? [];
                @endphp

                <!-- Row 1: 4 Stat Cards -->
                <div class="custom-grid-4">
                    
                    <!-- Performance Card -->
                    <div class="bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-600 rounded-xl shadow-sm p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs lg:text-sm font-medium text-slate-500 dark:text-gray-400">Performance</h3>
                            <span class="text-[10px] lg:text-xs px-2 py-1 rounded-full {{ $score >= 90 ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400' : ($score >= 50 ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400' : 'bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-400') }}">
                                {{ $score >= 90 ? 'Excellent' : ($score >= 50 ? 'Average' : 'Critical') }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-3">
                            <span class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white">{{ $score }}</span>
                            <span class="text-base lg:text-lg text-slate-400 dark:text-gray-500">/100</span>
                        </div>
                        <div class="border-t border-slate-100 dark:border-gray-700 pt-2">
                            <div class="h-12 lg:h-16">
                                <canvas id="performanceSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[9px] lg:text-[10px] text-slate-400 dark:text-gray-500 mt-1">
                                <span>7 Days</span>
                                <span>Today</span>
                            </div>
                        </div>
                    </div>

                    <!-- Uptime Card -->
                    <div class="bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-600 rounded-xl shadow-sm p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs lg:text-sm font-medium text-slate-500 dark:text-gray-400">Uptime</h3>
                            <span class="text-[10px] lg:text-xs px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400">30 Days</span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-3">
                            <span class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white">{{ number_format($uptime, 1) }}</span>
                            <span class="text-base lg:text-lg text-slate-400 dark:text-gray-500">%</span>
                        </div>
                        <div class="border-t border-slate-100 dark:border-gray-700 pt-2">
                            <div class="h-12 lg:h-16">
                                <canvas id="uptimeSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[9px] lg:text-[10px] text-slate-400 dark:text-gray-500 mt-1">
                                <span>30 Days</span>
                                <span>Today</span>
                            </div>
                        </div>
                    </div>

                    <!-- Response Time Card -->
                    <div class="bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-600 rounded-xl shadow-sm p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs lg:text-sm font-medium text-slate-500 dark:text-gray-400">Response Time</h3>
                            <span class="text-[10px] lg:text-xs px-2 py-1 rounded-full {{ $avgResponseTime < 300 ? 'bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-400' : 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400' }}">
                                {{ $avgResponseTime < 300 ? 'Fast' : 'Acceptable' }}
                            </span>
                        </div>
                        <div class="flex items-baseline gap-1 mb-3">
                            <span class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white">
                                @if(isset($avgResponseTime))
                                    @if($avgResponseTime < 1000)
                                        {{ $avgResponseTime }}<span class="text-lg lg:text-xl text-slate-500 font-medium">ms</span>
                                    @else
                                        {{ number_format($avgResponseTime / 1000, 2) }}<span class="text-lg lg:text-xl text-slate-500 font-medium">s</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="border-t border-slate-100 dark:border-gray-700 pt-2">
                            <div class="h-12 lg:h-16">
                                <canvas id="responseSparkline" class="w-full h-full"></canvas>
                            </div>
                            <div class="flex justify-between text-[9px] lg:text-[10px] text-slate-400 dark:text-gray-500 mt-1">
                                <span>7 Days</span>
                                <span>Today</span>
                            </div>
                        </div>
                    </div>

                    <!-- SSL Certificate Card -->
                    <div class="bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-600 rounded-xl shadow-sm p-4">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xs lg:text-sm font-medium text-slate-500 dark:text-gray-400">SSL Certificate</h3>
                                <svg class="w-4 h-4 lg:w-5 lg:h-5 {{ $sslDaysRemaining > 30 ? 'text-emerald-500' : ($sslDaysRemaining > 7 ? 'text-amber-500' : 'text-rose-500') }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="flex items-baseline gap-1 mb-3">
                                <span class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white">{{ $sslDaysRemaining }}</span>
                                <span class="text-base lg:text-lg text-slate-400 dark:text-gray-500">Days</span>
                            </div>
                        </div>
                        <div class="border-t border-slate-100 dark:border-gray-700 pt-2">
                            <div class="relative pt-1">
                                <div class="overflow-hidden h-3 text-xs flex rounded bg-slate-100 dark:bg-gray-700">
                                    <div style="width:{{ min(100, ($sslDaysRemaining / 90) * 100) }}%" 
                                         class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $sslDaysRemaining > 60 ? 'bg-emerald-500' : ($sslDaysRemaining > 30 ? 'bg-emerald-400' : ($sslDaysRemaining > 7 ? 'bg-amber-500' : 'bg-rose-500')) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-between text-[9px] lg:text-[10px] text-slate-400 dark:text-gray-500 mt-2">
                                <span>0 Days</span>
                                <span>90 Days</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Geräte + Traffic -->
                <div class="custom-grid-2">
                    
                    <!-- Device Pie Chart (Left) -->
                    <div class="min-w-0 bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-600 rounded-xl shadow-sm p-4 lg:p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-slate-800 dark:text-gray-100">Devices</h3>
                            <span class="text-xs text-slate-400 dark:text-gray-500">Last 30 Days</span>
                        </div>
                        <div class="flex items-center justify-center">
                            <div class="relative w-48 h-48 max-w-full">
                                <canvas id="overviewDeviceChart"></canvas>
                            </div>
                        </div>
                        <!-- Legend -->
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-violet-500"></div>
                                    <span class="text-slate-600 dark:text-gray-400">Desktop</span>
                                </div>
                                <span class="font-medium text-slate-800 dark:text-gray-200">{{ $deviceStats['desktop'] ?? 0 }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-cyan-500"></div>
                                    <span class="text-slate-600 dark:text-gray-400">Mobile</span>
                                </div>
                                <span class="font-medium text-slate-800 dark:text-gray-200">{{ $deviceStats['mobile'] ?? 0 }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                    <span class="text-slate-600 dark:text-gray-400">Tablet</span>
                                </div>
                                <span class="font-medium text-slate-800 dark:text-gray-200">{{ $deviceStats['tablet'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Traffic Line Chart (Right) — click opens Analytics tab -->
                    <div class="min-w-0 bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-600 rounded-xl shadow-sm p-4 lg:p-5 cursor-pointer hover:border-violet-200 dark:hover:border-cyan-500/30 transition-colors"
                         @click="tab = 'analytics'" role="button" tabindex="0" @keydown.enter="tab = 'analytics'">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-slate-800 dark:text-gray-100">Traffic Overview</h3>
                            <div class="flex items-center gap-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-violet-500"></div>
                                    <span class="text-slate-500 dark:text-gray-400">Visitors</span>
                                    <span class="font-semibold text-slate-800 dark:text-gray-200">{{ number_format(array_sum($chartVisitors)) }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-cyan-500"></div>
                                    <span class="text-slate-500 dark:text-gray-400">Pageviews</span>
                                    <span class="font-semibold text-slate-800 dark:text-gray-200">{{ number_format(array_sum($chartPageviews)) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="relative h-64 w-full">
                            <canvas id="overviewChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Security Alert (nur wenn Issues) -->
                @if($criticalCount + $warningCount > 0)
                <div @click="showSecurityModal = true"
                     class="card-base p-4 flex items-center gap-3 cursor-pointer transition-all duration-200 bg-rose-50 dark:bg-red-500/10 border-rose-200 dark:border-red-500/30 hover:bg-rose-100 dark:hover:bg-red-500/20 hover:border-rose-300 dark:hover:border-red-500/50 hover:shadow-lg dark:hover:shadow-red-500/20 group">
                    <svg class="w-6 h-6 text-rose-600 dark:text-red-400 flex-shrink-0 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div class="flex-1">
                        <span class="font-bold text-rose-700 dark:text-red-400 group-hover:text-rose-800 dark:group-hover:text-red-300">{{ $criticalCount + $warningCount }} security issues found</span>
                        <span class="text-sm ml-2 text-slate-600 dark:text-gray-400 group-hover:underline">→ Click for details</span>
                    </div>
                    <svg class="w-5 h-5 text-slate-400 dark:text-gray-500 group-hover:text-rose-600 dark:group-hover:text-red-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
                @endif

                @if(count($monitoredUrls) > 0)
                <!-- Monitored Paths Summary -->
                <div class="card-base p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-violet-100 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 dark:text-white">Monitored Paths</h3>
                                <p class="text-xs text-slate-500 dark:text-gray-400">Current status of your subpages</p>
                            </div>
                        </div>
                        <button @click="tab = 'monitoring'" class="text-xs font-bold text-violet-600 dark:text-violet-400 hover:underline">Manage all →</button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($monitoredUrls as $mUrl)
                        <div class="p-3 rounded-xl border border-slate-100 dark:border-gray-700/50 bg-slate-50/50 dark:bg-gray-800/30 flex items-center justify-between group hover:border-violet-200 dark:hover:border-violet-500/30 transition-all">
                            <div class="min-w-0 pr-4">
                                <p class="text-[10px] font-mono text-slate-400 uppercase tracking-tighter">{{ parse_url($mUrl->url, PHP_URL_PATH) ?: '/' }}</p>
                                <p class="text-xs font-semibold text-slate-700 dark:text-gray-200 truncate" title="{{ $mUrl->url }}">{{ $mUrl->url }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <p class="text-[10px] font-bold {{ $mUrl->last_status_code < 400 ? 'text-emerald-500' : 'text-rose-500' }}">
                                        {{ $mUrl->last_status_code ?: 'PENDING' }}
                                    </p>
                                    <p class="text-[9px] text-slate-400 font-mono">
                                        @if(isset($mUrl->last_response_time))
                                            @if($mUrl->last_response_time < 1000)
                                                {{ $mUrl->last_response_time }}ms
                                            @else
                                                {{ number_format($mUrl->last_response_time / 1000, 2) }}s
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                                <div class="w-2 h-2 rounded-full {{ $mUrl->last_status_code < 400 ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Audit Details - Grouped by Category with Messages -->
                @if(!empty($auditDetails))
                @php
                    $categories = [
                        'performance' => ['name' => 'Performance', 'icon' => '⚡', 'color' => '#f97316'],
                        'seo' => ['name' => 'SEO', 'icon' => '🔍', 'color' => '#8b5cf6'],
                        'accessibility' => ['name' => 'Accessibility', 'icon' => '♿', 'color' => '#06b6d4'],
                        'security' => ['name' => 'Security', 'icon' => '🔒', 'color' => '#22c55e'],
                    ];
                    $groupedAudits = collect($auditDetails)->groupBy('category');
                    $passedCount = collect($auditDetails)->where('status', 'success')->count();
                    $failedCount = collect($auditDetails)->where('status', '!=', 'success')->count();
                @endphp
                
                <div class="card-base p-4 sm:p-6 group">
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-primary font-bold text-lg flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gradient-to-br from-violet-500 to-cyan-500 dark:from-violet-600 dark:to-cyan-400">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                </div>
                                Website Audit
                            </h3>
                            <div class="flex items-center gap-3 text-sm">
                                <span class="px-3 py-1 rounded-full bg-emerald-100 dark:bg-green-500/15 text-emerald-700 dark:text-green-400">✓ {{ $passedCount }} passed</span>
                                @if($failedCount > 0)
                                    <span class="px-3 py-1 rounded-full bg-rose-100 dark:bg-red-500/15 text-rose-700 dark:text-red-400">✗ {{ $failedCount }} issues</span>
                                @endif
                            </div>
                        </div>

                        <!-- Categories -->
                        <div class="space-y-4">
                            @foreach($categories as $catKey => $cat)
                                @if(isset($groupedAudits[$catKey]))
                                    @php
                                        $items = $groupedAudits[$catKey];
                                        $catPassed = $items->where('status', 'success')->count();
                                        $catFailed = $items->where('status', '!=', 'success')->count();
                                    @endphp
                                    <div x-data="{ open: {{ $catFailed > 0 ? 'true' : 'false' }} }" class="rounded-lg overflow-hidden border border-slate-200 dark:border-gray-700/50">
                                        <!-- Category Header -->
                                        <button @click="open = !open" class="w-full flex items-center justify-between p-4 transition-all bg-slate-50 dark:bg-gray-800/50 hover:bg-slate-100 dark:hover:bg-gray-800">
                                            <div class="flex items-center gap-3">
                                                <span class="text-xl">{{ $cat['icon'] }}</span>
                                                <span class="font-bold text-primary">{{ $cat['name'] }}</span>
                                                <span class="text-xs px-2 py-0.5 rounded {{ $catFailed > 0 ? 'bg-rose-100 dark:bg-red-500/20 text-rose-600 dark:text-red-400' : 'bg-emerald-100 dark:bg-green-500/20 text-emerald-600 dark:text-green-400' }}">
                                                    {{ $catPassed }}/{{ $items->count() }}
                                                </span>
                                            </div>
                                            <svg class="w-5 h-5 text-slate-400 dark:text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>
                                        
                                        <!-- Category Items -->
                                        <div x-show="open" x-collapse class="divide-y divide-slate-200 dark:divide-gray-700/30">
                                            @foreach($items as $item)
                                                <div class="p-4 flex items-start gap-3 bg-slate-50/50 dark:bg-gray-900/30">
                                                    @if(($item['status'] ?? '') === 'success')
                                                        <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center bg-emerald-100 dark:bg-green-500/20">
                                                            <span class="text-emerald-600 dark:text-green-400 text-sm">✓</span>
                                                        </div>
                                                        <div>
                                                            <p class="text-primary font-medium">{{ $item['label'] ?? '' }}</p>
                                                            <p class="text-muted text-sm mt-0.5">{{ $item['message'] ?? '' }}</p>
                                                        </div>
                                                    @else
                                                        <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center bg-rose-100 dark:bg-red-500/20">
                                                            <span class="text-rose-600 dark:text-red-400 text-sm">!</span>
                                                        </div>
                                                        <div class="flex-1">
                                                            <p class="text-rose-700 dark:text-red-300 font-medium">{{ $item['label'] ?? '' }}</p>
                                                            <p class="text-rose-600/80 dark:text-red-400/80 text-sm mt-0.5">{{ $item['message'] ?? '' }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

            </div>
