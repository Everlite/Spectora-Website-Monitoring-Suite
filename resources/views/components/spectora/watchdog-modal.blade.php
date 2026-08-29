<div x-show="isWatchdogOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isWatchdogOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-[#070B12]/80 backdrop-blur-md transition-opacity" 
             @click="closeWatchdog()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="isWatchdogOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-[#131B2E] border border-slate-700/80 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Spectora Watchdog Heuristics Report</h3>
                            <p class="text-xs text-cyan-400 font-mono" x-text="watchdogUrl"></p>
                        </div>
                    </div>
                    <button type="button" @click="closeWatchdog()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Body Content -->
                <div class="mt-5 max-h-[60vh] overflow-y-auto pr-1">
                    <!-- Safe Banner -->
                    <template x-if="watchdogType === 'safe' || (watchdogDetails && watchdogDetails.watchdog && watchdogDetails.watchdog.status === 'safe')">
                        <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl p-5 text-center">
                            <div class="w-12 h-12 rounded-full bg-emerald-500/20 text-emerald-400 mx-auto flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h4 class="text-base font-bold text-white">All Watchdog Checks Passed</h4>
                            <p class="text-xs text-slate-300 mt-1">No malware signatures, obfuscated payloads, defacements, or cloaking detected on this domain.</p>
                        </div>
                    </template>

                    <!-- Threats / Issues List -->
                    <template x-if="watchdogType !== 'safe'">
                        <div class="space-y-4">
                            <!-- Keyword Missing Warning -->
                            <template x-if="watchdogDetails && watchdogDetails.keywords_missing">
                                <div class="bg-rose-500/10 border border-rose-500/30 rounded-xl p-4">
                                    <div class="text-xs font-bold uppercase tracking-wider text-rose-400 mb-1">Required Keyword Missing</div>
                                    <div class="text-sm text-white" x-text="'Expected string not found: ' + watchdogDetails.keywords_missing.join(', ')"></div>
                                </div>
                            </template>

                            <!-- Keyword Found Error -->
                            <template x-if="watchdogDetails && watchdogDetails.keywords_found">
                                <div class="bg-rose-500/10 border border-rose-500/30 rounded-xl p-4">
                                    <div class="text-xs font-bold uppercase tracking-wider text-rose-400 mb-1">Forbidden Error Keyword Detected</div>
                                    <div class="text-sm text-white" x-text="'Forbidden string found in HTML: ' + watchdogDetails.keywords_found.join(', ')"></div>
                                </div>
                            </template>

                            <!-- Watchdog Heuristic Issues -->
                            <template x-for="issue in (watchdogDetails && watchdogDetails.watchdog ? watchdogDetails.watchdog.issues : [])" :key="issue.type + issue.title">
                                <div class="bg-[#0B0F17] border rounded-xl p-4.5"
                                     :class="{
                                         'border-rose-500/40 bg-rose-500/5': issue.severity === 'critical',
                                         'border-amber-500/40 bg-amber-500/5': issue.severity === 'warning',
                                         'border-slate-800': issue.severity === 'info'
                                     }">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider"
                                                  :class="{
                                                      'bg-rose-500/20 text-rose-400': issue.severity === 'critical',
                                                      'bg-amber-500/20 text-amber-400': issue.severity === 'warning',
                                                      'bg-cyan-500/20 text-cyan-400': issue.severity === 'info'
                                                  }" x-text="issue.severity"></span>
                                            <h5 class="text-sm font-bold text-white" x-text="issue.title"></h5>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-300 mt-2" x-text="issue.description"></p>
                                    
                                    <template x-if="issue.context">
                                        <div class="mt-2.5 bg-[#070B12] border border-slate-800 rounded-lg p-2.5 text-[11px] font-mono text-slate-400 break-all" x-text="issue.context"></div>
                                    </template>

                                    <template x-if="issue.recommendation">
                                        <div class="mt-2 text-[11px] text-cyan-400 font-medium flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span x-text="'Recommendation: ' + issue.recommendation"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-between pt-4 mt-5 border-t border-slate-800">
                    <button type="button" @click="copyWatchdogJson()" class="text-xs text-slate-400 hover:text-cyan-400 font-mono transition-colors flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                        <span id="wd-copy-text">Copy Raw Telemetry</span>
                    </button>
                    <button type="button" @click="closeWatchdog()" class="btn-cyber-secondary">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
