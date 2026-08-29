<div x-show="isDeleteModalOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isDeleteModalOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" 
             @click="closeDeleteModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="isDeleteModalOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-[#0F1626] border border-[#1E293B] rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
            
            <div class="p-6">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-rose-950/60 border border-rose-800/40 flex items-center justify-center shrink-0 text-rose-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Löschen bestätigen</h3>
                        <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                            Möchtest du <span x-text="deleteLabel" class="font-semibold text-white font-mono"></span> wirklich entfernen? Alle Check-Historien und Telemetrie-Daten werden gelöscht.
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 mt-5 border-t border-[#1E293B]">
                    <button type="button" @click="closeDeleteModal()" class="btn-premium-secondary">
                        Abbrechen
                    </button>
                    <button type="button" @click="submitDelete()" class="inline-flex items-center justify-center px-3.5 py-2 rounded-lg font-semibold text-xs text-white bg-rose-600 hover:bg-rose-500 transition-colors shadow-sm">
                        Endgültig löschen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
