<div x-data="addDomainManager()"
     @open-add-domain.window="openModal()"
     x-show="isOpen"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" 
             @click="closeModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-[#0F1626] border border-[#1E293B] rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <form method="POST" action="{{ route('domains.store') }}" class="p-6">
                @csrf
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-950/60 border border-blue-800/40 flex items-center justify-center text-blue-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white">Neue Website überwachen</h3>
                            <p class="text-xs text-slate-400">Automatisierte Uptime-Probes & Pulse-Telemetrie</p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- URL -->
                <div class="mb-4">
                    <label for="url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Website URL <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="url" id="url" required placeholder="https://kunden-website.de"
                           class="w-full bg-[#070B13] border border-[#1E293B] rounded-lg px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors">
                </div>

                <!-- Keyword Must Contain -->
                <div class="mb-4">
                    <label for="keyword_must_contain" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Muss Keyword enthalten <span class="text-slate-500 text-[10px] font-normal">(optional)</span>
                    </label>
                    <input type="text" name="keyword_must_contain" id="keyword_must_contain" placeholder="z. B. Willkommen, Copyright 2026"
                           class="w-full bg-[#070B13] border border-[#1E293B] rounded-lg px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors">
                </div>

                <!-- Keyword Must Not Contain -->
                <div class="mb-5">
                    <label for="keyword_must_not_contain" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Darf Keyword NICHT enthalten <span class="text-slate-500 text-[10px] font-normal">(optional)</span>
                    </label>
                    <input type="text" name="keyword_must_not_contain" id="keyword_must_not_contain" placeholder="z. B. Error 500, Database connection failed"
                           class="w-full bg-[#070B13] border border-[#1E293B] rounded-lg px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-[#1E293B]">
                    <button type="button" @click="closeModal()" class="btn-premium-secondary">
                        Abbrechen
                    </button>
                    <button type="submit" class="btn-premium-primary">
                        Überwachung starten
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
