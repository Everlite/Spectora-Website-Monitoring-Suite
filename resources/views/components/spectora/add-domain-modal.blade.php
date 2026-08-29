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
             class="fixed inset-0 bg-[#080F27]/80 backdrop-blur-md" 
             @click="closeModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Horizon Modal Panel -->
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="inline-block align-bottom bg-[#111C44] border border-[#1B254B] rounded-horizon text-left overflow-hidden shadow-horizon-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <form method="POST" action="{{ route('domains.store') }}" class="p-6 space-y-4">
                @csrf
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-4 border-b border-[#1B254B]">
                    <div>
                        <h3 class="text-base font-extrabold text-white">Neue Ziel-Website hinzufügen</h3>
                        <p class="text-xs text-[#A3AED0] mt-0.5">Automatisierte Uptime-Probes, SSL-Wächter & Telemetrie.</p>
                    </div>
                    <button type="button" @click="closeModal()" class="text-[#A3AED0] hover:text-white p-1 rounded-full hover:bg-[#1B254B]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- URL -->
                <div class="space-y-1.5">
                    <label for="url" class="text-xs font-bold text-white">
                        Website URL <span class="text-[#EE5D50]">*</span>
                    </label>
                    <input type="text" name="url" id="url" required placeholder="https://kunden-website.de"
                           class="horizon-input">
                </div>

                <!-- Keyword Must Contain -->
                <div class="space-y-1.5">
                    <label for="keyword_must_contain" class="text-xs font-bold text-white">
                        Muss Keyword enthalten <span class="text-[#A3AED0] text-[10px] font-normal">(optional)</span>
                    </label>
                    <input type="text" name="keyword_must_contain" id="keyword_must_contain" placeholder="z. B. Willkommen, Copyright"
                           class="horizon-input">
                </div>

                <!-- Keyword Must Not Contain -->
                <div class="space-y-1.5">
                    <label for="keyword_must_not_contain" class="text-xs font-bold text-white">
                        Darf Keyword NICHT enthalten <span class="text-[#A3AED0] text-[10px] font-normal">(optional)</span>
                    </label>
                    <input type="text" name="keyword_must_not_contain" id="keyword_must_not_contain" placeholder="z. B. Error 500, Database connection failed"
                           class="horizon-input">
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#1B254B] mt-4">
                    <button type="button" @click="closeModal()" class="btn-horizon-secondary">
                        Abbrechen
                    </button>
                    <button type="submit" class="btn-horizon-primary">
                        Überwachung starten
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
