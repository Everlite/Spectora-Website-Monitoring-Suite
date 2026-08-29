<div x-show="isWatchdogOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isWatchdogOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/80 backdrop-blur-sm" 
             @click="closeWatchdog()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- shadcn Dialog Panel -->
        <div x-show="isWatchdogOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="inline-block align-bottom bg-card border border-border rounded-xl text-left overflow-hidden shadow-shadcn-lg transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full">
            
            <div class="p-6 space-y-4">
                <!-- Header -->
                <div class="flex items-start justify-between pb-3 border-b border-border">
                    <div class="space-y-0.5">
                        <h3 class="text-base font-semibold text-foreground">Watchdog Sicherheits-Prüfbericht</h3>
                        <p class="text-xs text-muted-foreground font-mono" x-text="watchdogUrl"></p>
                    </div>
                    <button type="button" @click="closeWatchdog()" class="text-muted-foreground hover:text-foreground p-1 rounded-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Body Content -->
                <div class="max-h-[60vh] overflow-y-auto space-y-3 pr-1">
                    <!-- Safe State -->
                    <template x-if="watchdogType === 'safe' || (watchdogDetails && watchdogDetails.watchdog && watchdogDetails.watchdog.status === 'safe')">
                        <div class="rounded-lg border border-emerald-800/40 bg-emerald-950/20 p-4 text-center space-y-1.5">
                            <div class="w-8 h-8 rounded-full bg-emerald-950/60 text-emerald-400 mx-auto flex items-center justify-center border border-emerald-800/40">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h4 class="text-xs font-bold text-foreground">Keine Bedrohungen erkannt</h4>
                            <p class="text-[11px] text-muted-foreground">Heuristik-Check für Malware-Payloads, Defacements und Cloaking abgeschlossen.</p>
                        </div>
                    </template>

                    <!-- Threats State -->
                    <template x-if="watchdogType !== 'safe'">
                        <div class="space-y-2.5">
                            <template x-for="issue in (watchdogDetails && watchdogDetails.watchdog ? watchdogDetails.watchdog.issues : [])" :key="issue.type + issue.title">
                                <div class="rounded-lg border border-border bg-zinc-950 p-3.5 space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase"
                                              :class="{
                                                  'bg-rose-950/60 text-rose-400 border border-rose-800/40': issue.severity === 'critical',
                                                  'bg-amber-950/60 text-amber-400 border border-amber-800/40': issue.severity === 'warning',
                                                  'bg-secondary text-muted-foreground border border-border': issue.severity === 'info'
                                              }" x-text="issue.severity"></span>
                                        <h5 class="text-xs font-semibold text-foreground" x-text="issue.title"></h5>
                                    </div>
                                    <p class="text-xs text-muted-foreground" x-text="issue.description"></p>
                                    
                                    <template x-if="issue.context">
                                        <pre class="bg-zinc-900 border border-border rounded p-2 text-[10px] font-mono text-zinc-400 overflow-x-auto break-all" x-text="issue.context"></pre>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between pt-3 border-t border-border mt-4">
                    <button type="button" @click="copyWatchdogJson()" class="text-xs text-muted-foreground hover:text-foreground font-mono transition-colors">
                        JSON kopieren
                    </button>
                    <button type="button" @click="closeWatchdog()" class="btn-secondary">
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
