<div x-show="isNotesOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isNotesOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-[#080F27]/80 backdrop-blur-md" 
             @click="closeNotes()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Horizon Modal Panel -->
        <div x-show="isNotesOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="inline-block align-bottom bg-[#111C44] border border-[#1B254B] rounded-horizon text-left overflow-hidden shadow-horizon-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <div class="p-6 space-y-4">
                <!-- Header -->
                <div class="flex items-center justify-between pb-4 border-b border-[#1B254B]">
                    <div>
                        <h3 class="text-base font-extrabold text-white">Team-Notizen</h3>
                        <p class="text-xs text-[#A3AED0] font-mono mt-0.5" x-text="domainUrl"></p>
                    </div>
                    <button type="button" @click="closeNotes()" class="text-[#A3AED0] hover:text-white p-1 rounded-full hover:bg-[#1B254B]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Add Note Form -->
                <div class="space-y-2.5">
                    <textarea x-model="newNote" 
                              rows="3" 
                              placeholder="Notiz für das Team hinterlassen..."
                              class="flex w-full rounded-horizon-sm border border-[#1B254B] bg-[#0B1437] px-4 py-3 text-xs text-white placeholder-[#A3AED0] focus:border-[#7551FF] focus:outline-none focus:ring-1 focus:ring-[#7551FF]"></textarea>
                    <div class="flex justify-end">
                        <button type="button" 
                                @click="addNote()" 
                                :disabled="!newNote.trim()" 
                                class="btn-horizon-primary disabled:opacity-40">
                            Notiz speichern
                        </button>
                    </div>
                </div>

                <!-- Notes List -->
                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    <template x-for="note in notes" :key="note.id">
                        <div class="rounded-horizon-sm border border-[#1B254B] bg-[#0B1437] p-3.5 space-y-1.5">
                            <p class="text-xs text-white whitespace-pre-wrap leading-relaxed" x-text="note.content"></p>
                            <div class="text-[10px] text-[#A3AED0] flex items-center justify-between border-t border-[#1B254B] pt-2 mt-2">
                                <span x-text="(note.user ? note.user.first_name + ' ' + note.user.last_name : 'Team') + ' · ' + formatDate(note.created_at)"></span>
                                <div class="flex gap-2.5">
                                    <button @click="editNote(note)" class="text-white hover:text-[#7551FF] font-bold">Bearbeiten</button>
                                    <button @click="deleteNote(note.id)" class="text-[#EE5D50] hover:underline font-bold">Löschen</button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="notes.length === 0" class="text-center text-[#A3AED0] text-xs py-6">
                        Noch keine Notizen vorhanden.
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end pt-4 border-t border-[#1B254B]">
                    <button type="button" @click="closeNotes()" class="btn-horizon-secondary">
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
