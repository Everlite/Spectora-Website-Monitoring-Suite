<div x-data="addDomainManager()"
     @open-add-domain.window="openModal()"
     x-show="isOpen"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/80 backdrop-blur-sm" 
             @click="closeModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Studio Modal Panel -->
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="inline-block align-bottom bg-[#111622] border border-[#202A3E] rounded-studio text-left overflow-hidden shadow-studio-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <form method="POST" action="{{ route('domains.store') }}" class="p-6 space-y-4">
                @csrf
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-3 border-b border-[#202A3E]">
                    <div>
                        <h3 class="text-sm font-bold text-white">Neue Ziel-Website hinzufügen</h3>
                        <p class="text-xs text-[#8A95A8] mt-0.5">Automatisierte Uptime-Probes, SSL-Wächter & Telemetrie.</p>
                    </div>
                    <button type="button" @click="closeModal()" class="text-[#8A95A8] hover:text-white p-1 rounded-studio-sm hover:bg-[#171E2E]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- URL -->
                <div class="space-y-1.5">
                    <label for="url" class="text-xs font-bold text-white">
                        Website URL <span class="text-[#F43F5E]">*</span>
                    </label>
                    <input type="text" name="url" id="url" required placeholder="https://kunden-website.de"
                           class="spectora-input">
                </div>

                <!-- Keyword Must Contain -->
                <div class="space-y-1.5">
                    <label for="keyword_must_contain" class="text-xs font-bold text-white">
                        Muss Keyword enthalten <span class="text-[#8A95A8] text-[10px] font-normal">(optional)</span>
                    </label>
                    <input type="text" name="keyword_must_contain" id="keyword_must_contain" placeholder="z. B. Willkommen, Copyright"
                           class="spectora-input">
                </div>

                <!-- Keyword Must Not Contain -->
                <div class="space-y-1.5">
                    <label for="keyword_must_not_contain" class="text-xs font-bold text-white">
                        Darf Keyword NICHT enthalten <span class="text-[#8A95A8] text-[10px] font-normal">(optional)</span>
                    </label>
                    <input type="text" name="keyword_must_not_contain" id="keyword_must_not_contain" placeholder="z. B. Error 500, Database connection failed"
                           class="spectora-input">
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-[#202A3E] mt-4">
                    <button type="button" @click="closeModal()" class="btn-spectora-secondary">
                        Abbrechen
                    </button>
                    <button type="submit" class="btn-spectora-primary">
                        Überwachung starten
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
