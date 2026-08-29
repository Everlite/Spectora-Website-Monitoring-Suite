<div x-data="addDomainManager()"
     @open-add-domain.window="openModal()"
     x-show="isOpen"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-[#070B12]/80 backdrop-blur-md transition-opacity" 
             @click="closeModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-[#131B2E] border border-slate-700/80 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <form method="POST" action="{{ route('domains.store') }}" class="p-6">
                @csrf
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Add Monitored Domain</h3>
                            <p class="text-xs text-slate-400">Initialize monitoring and automated Spectora Engine audit.</p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- URL -->
                <div class="mb-4">
                    <label for="url" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Domain / Website URL <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="url" id="url" required placeholder="https://client-website.com"
                           class="w-full bg-[#0B0F17] border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-colors">
                    <p class="mt-1 text-[11px] text-slate-500">Enter with or without https:// (SSRF protected).</p>
                </div>

                <!-- Keyword Must Contain -->
                <div class="mb-4">
                    <label for="keyword_must_contain" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Keyword Must Contain <span class="text-slate-500 text-[10px] lowercase">(optional)</span>
                    </label>
                    <input type="text" name="keyword_must_contain" id="keyword_must_contain" placeholder="e.g. Welcome, Copyright 2026"
                           class="w-full bg-[#0B0F17] border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-colors">
                </div>

                <!-- Keyword Must Not Contain -->
                <div class="mb-5">
                    <label for="keyword_must_not_contain" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Keyword Must NOT Contain <span class="text-slate-500 text-[10px] lowercase">(optional)</span>
                    </label>
                    <input type="text" name="keyword_must_not_contain" id="keyword_must_not_contain" placeholder="e.g. Error 500, Database connection failed"
                           class="w-full bg-[#0B0F17] border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-colors">
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="closeModal()" class="btn-cyber-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="btn-cyber-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Start Monitoring
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
