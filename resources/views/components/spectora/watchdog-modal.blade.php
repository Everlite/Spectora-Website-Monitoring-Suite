<div x-show="isWatchdogOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isWatchdogOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-[#080F27]/80 backdrop-blur-md" 
             @click="closeWatchdog()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Horizon Modal Panel -->
        <div x-show="isWatchdogOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="inline-block align-bottom bg-[#111C44] border border-[#1B254B] rounded-horizon text-left overflow-hidden shadow-horizon-card transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
            
            <div class="p-6 space-y-4">
                <!-- Header -->
                <div class="flex items-start justify-between pb-4 border-b border-[#1B254B]">
                    <div>
                        <h3 class="text-base font-extrabold text-white">Watchdog Sicherheits-Prüfbericht</h3>
                        <p class="text-xs text-[#A3AED0] font-mono mt-0.5" x-text="watchdogUrl"></p>
                    </div>
                    <button type="button" @click="closeWatchdog()" class="text-[#A3AED0] hover:text-white p-1 rounded-full hover:bg-[#1B254B]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Body Content -->
                <div class="max-h-[60vh] overflow-y-auto space-y-3 pr-1">
                    <!-- Safe State -->
                    <template x-if="watchdogType === 'safe' || (watchdogDetails && watchdogDetails.watchdog && watchdogDetails.watchdog.status === 'safe')">
                        <div class="rounded-horizon bg-[#01B574]/20 border border-[#01B574]/40 p-5 text-center space-y-2">
                            <div class="w-10 h-10 rounded-full bg-[#01B574]/30 text-[#01B574] mx-auto flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-white">Keine Bedrohungen erkannt</h4>
                            <p class="text-xs text-[#A3AED0]">Heuristik-Scan für Malware-Payloads, Defacements und Cloaking abgeschlossen.</p>
                        </div>
                    </template>

                    <!-- Threats State -->
                    <template x-if="watchdogType !== 'safe'">
                        <div class="space-y-3">
                            <template x-for="issue in (watchdogDetails && watchdogDetails.watchdog ? watchdogDetails.watchdog.issues : [])" :key="issue.type + issue.title">
                                <div class="rounded-horizon-sm border border-[#1B254B] bg-[#0B1437] p-4 space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase"
                                              :class="{
                                                  'bg-[#EE5D50]/20 text-[#EE5D50] border border-[#EE5D50]/40': issue.severity === 'critical',
                                                  'bg-[#FFB547]/20 text-[#FFB547] border border-[#FFB547]/40': issue.severity === 'warning',
                                                  'bg-[#1B254B] text-[#A3AED0] border border-[#2B3674]': issue.severity === 'info'
                                              }" x-text="issue.severity"></span>
                                        <h5 class="text-xs font-bold text-white" x-text="issue.title"></h5>
                                    </div>
                                    <p class="text-xs text-[#A3AED0]" x-text="issue.description"></p>
                                    
                                    <template x-if="issue.context">
                                        <pre class="bg-[#111C44] border border-[#1B254B] rounded-horizon-sm p-2.5 text-[10px] font-mono text-[#A3AED0] overflow-x-auto break-all" x-text="issue.context"></pre>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between pt-4 border-t border-[#1B254B]">
                    <button type="button" @click="copyWatchdogJson()" class="text-xs text-[#A3AED0] hover:text-white font-mono transition-colors">
                        JSON kopieren
                    </button>
                    <button type="button" @click="closeWatchdog()" class="btn-horizon-secondary">
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
