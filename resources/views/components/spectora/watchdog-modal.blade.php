<div x-show="isWatchdogOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isWatchdogOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" 
             @click="closeWatchdog()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="isWatchdogOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-[#0F1626] border border-[#1E293B] rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-[#1E293B]">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <h3 class="text-base font-bold text-white">Watchdog Sicherheitsbericht</h3>
                        </div>
                        <p class="text-xs text-slate-400 font-mono mt-1" x-text="watchdogUrl"></p>
                    </div>
                    <button type="button" @click="closeWatchdog()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Body Content -->
                <div class="mt-4 max-h-[60vh] overflow-y-auto pr-1">
                    <!-- Safe Banner -->
                    <template x-if="watchdogType === 'safe' || (watchdogDetails && watchdogDetails.watchdog && watchdogDetails.watchdog.status === 'safe')">
                        <div class="bg-emerald-950/30 border border-emerald-800/40 rounded-xl p-5 text-center">
                            <div class="w-10 h-10 rounded-full bg-emerald-900/40 text-emerald-400 mx-auto flex items-center justify-center mb-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-white">Keine Bedrohungen erkannt</h4>
                            <p class="text-xs text-slate-400 mt-1">Keine Malware-Payloads, Defacements oder Cloaking-Texte auf dieser Domain gefunden.</p>
                        </div>
                    </template>

                    <!-- Threats List -->
                    <template x-if="watchdogType !== 'safe'">
                        <div class="space-y-3">
                            <template x-for="issue in (watchdogDetails && watchdogDetails.watchdog ? watchdogDetails.watchdog.issues : [])" :key="issue.type + issue.title">
                                <div class="bg-[#070B13] border border-[#1E293B] rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase font-mono"
                                              :class="{
                                                  'bg-rose-950/60 text-rose-400 border border-rose-800/40': issue.severity === 'critical',
                                                  'bg-amber-950/60 text-amber-400 border border-amber-800/40': issue.severity === 'warning',
                                                  'bg-slate-800 text-slate-300': issue.severity === 'info'
                                              }" x-text="issue.severity"></span>
                                        <h5 class="text-xs font-bold text-white" x-text="issue.title"></h5>
                                    </div>
                                    <p class="text-xs text-slate-300" x-text="issue.description"></p>
                                    
                                    <template x-if="issue.context">
                                        <pre class="mt-2 bg-[#04070D] border border-slate-800/60 rounded p-2 text-[11px] font-mono text-slate-400 break-all overflow-x-auto" x-text="issue.context"></pre>
                                    </template>

                                    <template x-if="issue.recommendation">
                                        <div class="mt-2 text-[11px] text-blue-400" x-text="'Empfehlung: ' + issue.recommendation"></div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between pt-4 mt-5 border-t border-[#1E293B]">
                    <button type="button" @click="copyWatchdogJson()" class="text-xs text-slate-400 hover:text-white font-mono transition-colors">
                        <span id="wd-copy-text">JSON kopieren</span>
                    </button>
                    <button type="button" @click="closeWatchdog()" class="btn-premium-secondary">
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
