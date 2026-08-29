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

        <!-- shadcn Dialog Panel -->
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="inline-block align-bottom bg-card border border-border rounded-xl text-left overflow-hidden shadow-shadcn-lg transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <form method="POST" action="{{ route('domains.store') }}" class="p-6 space-y-4">
                @csrf
                
                <!-- Dialog Header -->
                <div class="flex items-start justify-between pb-3 border-b border-border">
                    <div class="space-y-0.5">
                        <h3 class="text-base font-semibold text-foreground">Website zum Monitoring hinzufügen</h3>
                        <p class="text-xs text-muted-foreground">Automatisierte Uptime-Probes, SSL-Überwachung & Pulse-Telemetrie.</p>
                    </div>
                    <button type="button" @click="closeModal()" class="text-muted-foreground hover:text-foreground p-1 rounded-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- URL -->
                <div class="space-y-1.5">
                    <label for="url" class="text-xs font-medium text-foreground">
                        Website URL <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="url" id="url" required placeholder="https://kunden-domain.de"
                           class="shadcn-input">
                </div>

                <!-- Keyword Must Contain -->
                <div class="space-y-1.5">
                    <label for="keyword_must_contain" class="text-xs font-medium text-foreground">
                        Muss Keyword enthalten <span class="text-muted-foreground text-[10px]">(optional)</span>
                    </label>
                    <input type="text" name="keyword_must_contain" id="keyword_must_contain" placeholder="z. B. Willkommen, Copyright"
                           class="shadcn-input">
                </div>

                <!-- Keyword Must Not Contain -->
                <div class="space-y-1.5">
                    <label for="keyword_must_not_contain" class="text-xs font-medium text-foreground">
                        Darf Keyword NICHT enthalten <span class="text-muted-foreground text-[10px]">(optional)</span>
                    </label>
                    <input type="text" name="keyword_must_not_contain" id="keyword_must_not_contain" placeholder="z. B. Database connection failed, Error 500"
                           class="shadcn-input">
                </div>

                <!-- Dialog Footer -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-border mt-4">
                    <button type="button" @click="closeModal()" class="btn-outline">
                        Abbrechen
                    </button>
                    <button type="submit" class="btn-default">
                        Überwachung starten
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
