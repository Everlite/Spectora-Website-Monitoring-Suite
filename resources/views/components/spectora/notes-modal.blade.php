<div x-show="isNotesOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isNotesOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-black/80 backdrop-blur-sm" 
             @click="closeNotes()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Studio Modal Panel -->
        <div x-show="isNotesOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="inline-block align-bottom bg-[#111622] border border-[#202A3E] rounded-studio text-left overflow-hidden shadow-studio-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <div class="p-6 space-y-4">
                <!-- Header -->
                <div class="flex items-center justify-between pb-3 border-b border-[#202A3E]">
                    <div>
                        <h3 class="text-sm font-bold text-white">Team-Notizen</h3>
                        <p class="text-xs text-[#8A95A8] font-mono mt-0.5" x-text="domainUrl"></p>
                    </div>
                    <button type="button" @click="closeNotes()" class="text-[#8A95A8] hover:text-white p-1 rounded-studio-sm hover:bg-[#171E2E]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Add Note Form -->
                <div class="space-y-2">
                    <textarea x-model="newNote" 
                              rows="3" 
                              placeholder="Notiz für das Team (Deployments, DNS-Änderungen, Wartungsfenster)..."
                              class="flex w-full rounded-studio-sm border border-[#202A3E] bg-[#090B10] px-3.5 py-2.5 text-xs text-white placeholder-[#5A667A] focus:border-[#3B57E8] focus:outline-none focus:ring-1 focus:ring-[#3B57E8]"></textarea>
                    <div class="flex justify-end">
                        <button type="button" 
                                @click="addNote()" 
                                :disabled="!newNote.trim()" 
                                class="btn-spectora-primary disabled:opacity-40">
                            Notiz speichern
                        </button>
                    </div>
                </div>

                <!-- Notes Stream -->
                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    <template x-for="note in notes" :key="note.id">
                        <div class="rounded-studio-sm border border-[#202A3E] bg-[#090B10] p-3 space-y-1.5">
                            <p class="text-xs text-white whitespace-pre-wrap leading-relaxed" x-text="note.content"></p>
                            <div class="text-[10px] text-[#8A95A8] flex items-center justify-between border-t border-[#202A3E] pt-1.5 mt-1.5">
                                <span x-text="(note.user ? note.user.first_name + ' ' + note.user.last_name : 'Team') + ' · ' + formatDate(note.created_at)"></span>
                                <div class="flex gap-2">
                                    <button @click="editNote(note)" class="text-white hover:underline font-bold">Bearbeiten</button>
                                    <button @click="deleteNote(note.id)" class="text-[#F43F5E] hover:underline font-bold">Löschen</button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="notes.length === 0" class="text-center text-[#8A95A8] text-xs py-6">
                        Noch keine Notizen vorhanden.
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end pt-3 border-t border-[#202A3E] mt-4">
                    <button type="button" @click="closeNotes()" class="btn-spectora-secondary">
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
