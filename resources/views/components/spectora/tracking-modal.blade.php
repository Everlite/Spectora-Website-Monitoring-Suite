<div x-show="isTrackingOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isTrackingOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" 
             @click="closeTracking()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="isTrackingOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-[#0F1626] border border-[#1E293B] rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-[#1E293B]">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <h3 class="text-base font-bold text-white">Spectora Pulse Tracking Code</h3>
                        </div>
                        <p class="text-xs text-slate-400 mt-1 font-mono" x-text="trackingDomainUrl"></p>
                    </div>
                    <button type="button" @click="closeTracking()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="mt-5 space-y-4">
                    <!-- Instruction -->
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Füge dieses leichtgewichtige (< 1 KB) Telemetrie-Skript vor dem schließenden <code class="bg-[#070B13] px-1.5 py-0.5 rounded text-slate-300 font-mono">&lt;/head&gt;</code> oder <code class="bg-[#070B13] px-1.5 py-0.5 rounded text-slate-300 font-mono">&lt;/body&gt;</code> Tag der Kunden-Website ein. Es erfasst Seitenaufrufe und SPAs datenschutzkonform ohne Cookies.
                    </p>

                    <!-- Snippet Box -->
                    <div class="relative">
                        <pre class="bg-[#070B13] border border-[#1E293B] rounded-xl p-4 text-xs font-mono text-emerald-400 overflow-x-auto select-all leading-relaxed whitespace-pre-wrap" x-text="getSnippet()"></pre>
                        
                        <div class="absolute top-3 right-3">
                            <button type="button" 
                                    @click="copySnippet()" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-semibold text-white border border-slate-700 transition-colors shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                <span x-text="copied ? 'Kopiert!' : 'Code kopieren'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Custom Event Guide -->
                    <div class="bg-[#151F33] border border-[#27354E] rounded-xl p-4">
                        <h4 class="text-xs font-bold text-slate-200 uppercase tracking-wider mb-1.5">Optional: Custom Conversion Events</h4>
                        <p class="text-[11px] text-slate-400 mb-2">Du kannst Formular-Absendungen oder Button-Klicks auf der Website wie folgt erfassen:</p>
                        <pre class="bg-[#070B13] border border-[#1E293B] rounded-lg p-2.5 text-[11px] font-mono text-slate-300 overflow-x-auto">window.spectora.track('lead_form_submitted', { plan: 'pro' });</pre>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end pt-4 mt-5 border-t border-[#1E293B]">
                    <button type="button" @click="closeTracking()" class="btn-premium-secondary">
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
