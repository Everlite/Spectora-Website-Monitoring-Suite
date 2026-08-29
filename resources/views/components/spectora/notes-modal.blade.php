<div x-show="isNotesOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isNotesOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity" 
             @click="closeNotes()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="isNotesOpen" 
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-[#0F1626] border border-[#1E293B] rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between pb-4 border-b border-[#1E293B]">
                    <div>
                        <h3 class="text-base font-bold text-white">Team-Notizen</h3>
                        <p class="text-xs text-slate-400 font-mono mt-0.5" x-text="domainUrl"></p>
                    </div>
                    <button type="button" @click="closeNotes()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Add Note Form -->
                <div class="mt-4">
                    <textarea x-model="newNote" 
                              rows="3" 
                              placeholder="Notiz für das Team hinterlassen (Deployments, DNS-Änderungen, Absprachen)..."
                              class="w-full bg-[#070B13] border border-[#1E293B] rounded-lg p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-colors"></textarea>
                    <div class="mt-2.5 flex justify-end">
                        <button type="button" 
                                @click="addNote()" 
                                :disabled="!newNote.trim()" 
                                class="btn-premium-primary disabled:opacity-40">
                            Notiz speichern
                        </button>
                    </div>
                </div>

                <!-- Notes Stream -->
                <div class="mt-5 space-y-2.5 max-h-60 overflow-y-auto pr-1">
                    <template x-for="note in notes" :key="note.id">
                        <div class="bg-[#070B13] border border-[#1E293B] rounded-lg p-3">
                            <p class="text-xs text-slate-200 whitespace-pre-wrap leading-relaxed" x-text="note.content"></p>
                            <div class="mt-2 text-[10px] text-slate-500 flex items-center justify-between border-t border-slate-800/60 pt-1.5">
                                <span x-text="(note.user ? note.user.first_name + ' ' + note.user.last_name : 'Team') + ' · ' + formatDate(note.created_at)"></span>
                                <div class="flex gap-2">
                                    <button @click="editNote(note)" class="text-blue-400 hover:text-blue-300 font-medium">Bearbeiten</button>
                                    <button @click="deleteNote(note.id)" class="text-rose-400 hover:text-rose-300 font-medium">Löschen</button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="notes.length === 0" class="text-center text-slate-500 text-xs py-6">
                        Noch keine Notizen vorhanden.
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end pt-4 mt-5 border-t border-[#1E293B]">
                    <button type="button" @click="closeNotes()" class="btn-premium-secondary">
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
