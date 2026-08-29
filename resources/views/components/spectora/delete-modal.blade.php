<div x-show="isDeleteModalOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isDeleteModalOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/80 backdrop-blur-sm" 
             @click="closeDeleteModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- shadcn Alert Dialog Panel -->
        <div x-show="isDeleteModalOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="inline-block align-bottom bg-card border border-border rounded-xl text-left overflow-hidden shadow-shadcn-lg transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
            
            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-lg bg-rose-950/60 border border-rose-800/40 flex items-center justify-center shrink-0 text-rose-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-sm font-semibold text-foreground">Bist du sicher?</h3>
                        <p class="text-xs text-muted-foreground leading-relaxed">
                            Möchtest du <span x-text="deleteLabel" class="font-semibold text-foreground font-mono"></span> wirklich entfernen? Alle Check-Historien und Telemetrie-Messungen werden unwiderruflich gelöscht.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-border mt-4">
                    <button type="button" @click="closeDeleteModal()" class="btn-outline">
                        Abbrechen
                    </button>
                    <button type="button" @click="submitDelete()" class="btn-destructive">
                        Löschen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
