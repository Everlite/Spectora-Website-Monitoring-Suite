                    <!-- Tab Content: History -->
                    <div x-show="tab === 'history'"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-6"
                         x-data="historyManager()">
                        <div class="space-y-8">
                            <!-- Spectora Analysis Section -->
                            <div class="bg-spectora-card border border-gray-700/50 rounded-xl p-6 shadow-xl">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                                    <div>
                                        <h3 class="text-lg font-bold text-white">Spectora Analysis</h3>
                                        <p class="text-sm text-gray-400">Performance, Security & SEO Check</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <!-- Last Analysis Time -->
                                        @if($domain->updated_at)
                                            <span class="text-xs text-gray-500">
                                                Last Analysis: {{ $domain->updated_at->diffForHumans() }}
                                            </span>
                                        @endif
                                        
                                        <!-- Analysis Button with Loading State -->
                                        <button 
                                            @click="runAnalysis()"
                                            :disabled="isAnalyzing"
                                            class="bg-spectora-violet hover:bg-violet-600 disabled:bg-violet-800 disabled:cursor-wait text-white text-sm font-bold py-2 px-4 rounded transition shadow-lg flex items-center gap-2"
                                        >
                                            <template x-if="isAnalyzing">
                                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </template>
                                            <span x-text="isAnalyzing ? 'Analyzing...' : 'Start analysis'"></span>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-[10px] text-gray-500 italic bg-gray-800/30 p-2 rounded border border-gray-700/50 inline-block">
                                        <span class="text-spectora-cyan font-bold">Note:</span> Performance scan runs locally in a container – may take 30-60s.
                                    </p>
                                </div>
                                
                                <!-- Analysis Feedback -->
                                <div x-show="analysisResult" x-cloak class="mb-6">
                                    <div x-show="analysisResult === 'success'" class="bg-green-500/10 border border-green-500/30 rounded-lg p-4 flex items-center gap-3">
                                        <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <div>
                                            <p class="text-green-400 font-bold text-sm">Analysis successful!</p>
                                            <p class="text-green-300/70 text-xs">The page will be updated shortly...</p>
                                        </div>
                                    </div>
                                    <div x-show="analysisResult === 'error'" class="bg-red-500/10 border border-red-500/30 rounded-lg p-4 flex items-center gap-3">
                                        <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <div>
                                            <p class="text-red-400 font-bold text-sm">Analysis failed</p>
                                            <p class="text-red-300/70 text-xs" x-text="analysisError"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Details List -->
                                @if ($domain->last_pagespeed_details)
                                    <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                                        @foreach ($domain->last_pagespeed_details as $item)
                                            <div class="bg-gray-800/50 border border-gray-700 rounded-lg p-4 flex items-start gap-4">
                                                <div class="flex-shrink-0 mt-1">
                                                    @php $status = $item['status'] ?? 'unknown'; @endphp
                                                    @if ($status === 'success')
                                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    @elseif($status === 'warning')
                                                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-200 text-sm">{{ $item['label'] ?? 'Unknown Issue' }}</h4>
                                                    <p class="text-xs text-gray-400 mt-1">{{ $item['message'] ?? '' }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <p class="text-gray-500 italic">No analysis performed yet.</p>
                                        <p class="text-gray-600 text-sm mt-1">Click "Start analysis" for a detailed report.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Check History / Issues Log -->
                            <div class="bg-spectora-card border border-gray-700/50 rounded-xl p-6 shadow-xl">
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-white">Event Log</h3>
                                        <p class="text-sm text-gray-400">Issues and status changes</p>
                                    </div>
                                    
                                    <!-- Filter Toggle -->
                                    <div class="flex items-center gap-3">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" x-model="showAllLogs" class="sr-only peer">
                                            <div class="relative w-10 h-5 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-gray-400 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-spectora-cyan"></div>
                                            <span class="text-sm text-gray-400">Show all</span>
                                        </label>
                                    </div>
                                </div>
                                
                                @php
                                    // Filter only issues (Status >= 400 or 0)
                                    $issueChecks = $recentChecks->filter(function($check) {
                                        return $check->status_code >= 400 || $check->status_code === 0 || $check->status_code === null;
                                    });
                                @endphp
                                
                                <div class="overflow-x-auto">
                                    <!-- Issues Only View -->
                                    <div x-show="!showAllLogs">
                                        @if($issueChecks->count() > 0)
                                            <table class="w-full text-left">
                                                <thead>
                                                    <tr class="text-gray-500 text-xs uppercase tracking-wider border-b border-gray-700">
                                                        <th class="pb-3">Time</th>
                                                        <th class="pb-3">Status</th>
                                                        <th class="pb-3">Response time</th>
                                                        <th class="pb-3">Error message</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-700/50">
                                                    @foreach ($issueChecks as $check)
                                                        <tr class="hover:bg-gray-800/30 transition">
                                                            <td class="py-3 text-gray-300 text-sm">
                                                                {{ $check->created_at->format('d.m.Y H:i:s') }}</td>
                                                            <td class="py-3">
                                                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-red-500/20 text-red-400">
                                                                    {{ $check->status_code ?: 'ERROR' }}
                                                                </span>
                                                            </td>
                                                            <td class="py-3 text-gray-300 text-sm font-mono">
                                                                @if(isset($check->response_time))
                                                                    @if($check->response_time < 1)
                                                                        {{ round($check->response_time * 1000, 0) }}ms
                                                                    @else
                                                                        {{ number_format($check->response_time, 2) }}s
                                                                    @endif
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                            <td class="py-3 text-red-400 text-xs truncate max-w-[100px] sm:max-w-xs">
                                                                {{ $check->error_message ?? 'Connection error' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="text-center py-12">
                                                <svg class="w-16 h-16 mx-auto text-green-500/50 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <p class="text-green-400 font-bold text-lg">No issues!</p>
                                                <p class="text-gray-500 text-sm mt-1">No outages or errors recorded.</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- All Logs View -->
                                    <div x-show="showAllLogs" x-cloak>
                                        <table class="w-full text-left">
                                            <thead>
                                                <tr class="text-gray-500 text-xs uppercase tracking-wider border-b border-gray-700">
                                                    <th class="pb-3">Time</th>
                                                    <th class="pb-3">Status</th>
                                                    <th class="pb-3">Response time</th>
                                                    <th class="pb-3">Message</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-700/50">
                                                @foreach ($recentChecks as $check)
                                                    <tr class="hover:bg-gray-800/30 transition">
                                                        <td class="py-3 text-gray-300 text-sm">
                                                            {{ $check->created_at->format('d.m.Y H:i:s') }}</td>
                                                        <td class="py-3">
                                                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $check->status_code >= 200 && $check->status_code < 400 ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                                                {{ $check->status_code }}
                                                            </span>
                                                        </td>
                                                        <td class="py-3 text-gray-300 text-sm font-mono">
                                                            @if(isset($check->response_time))
                                                                @if($check->response_time < 1)
                                                                    {{ round($check->response_time * 1000, 0) }}ms
                                                                @else
                                                                    {{ number_format($check->response_time, 2) }}s
                                                                @endif
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td class="py-3 text-gray-400 text-xs truncate max-w-[100px] sm:max-w-xs">
                                                            {{ $check->error_message ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="mt-4">
                                            {{ $recentChecks->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
            </div>
