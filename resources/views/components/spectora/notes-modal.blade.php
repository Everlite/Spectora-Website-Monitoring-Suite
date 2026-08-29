<div x-show="isNotesOpen" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div x-show="isNotesOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-[#070B12]/80 backdrop-blur-md transition-opacity" 
             @click="closeNotes()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div x-show="isNotesOpen" 
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block align-bottom bg-[#131B2E] border border-slate-700/80 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <div class="p-6">
                <!-- Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-violet-500/10 border border-violet-500/30 flex items-center justify-center text-violet-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Domain Collaboration Notes</h3>
                            <p class="text-xs text-cyan-400 font-mono" x-text="domainUrl"></p>
                        </div>
                    </div>
                    <button type="button" @click="closeNotes()" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Add Note Form -->
                <div class="mt-4">
                    <textarea x-model="newNote" 
                              rows="3" 
                              placeholder="Write a team note regarding deployments, DNS, or client updates..."
                              class="w-full bg-[#0B0F17] border border-slate-700 rounded-xl p-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400 transition-colors"></textarea>
                    <div class="mt-2.5 flex justify-end">
                        <button type="button" 
                                @click="addNote()" 
                                :disabled="!newNote.trim()" 
                                class="btn-cyber-primary text-xs py-2 px-3.5 disabled:opacity-40">
                            Save Note
                        </button>
                    </div>
                </div>

                <!-- Notes Stream -->
                <div class="mt-6 space-y-3 max-h-64 overflow-y-auto pr-1">
                    <template x-for="note in notes" :key="note.id">
                        <div class="bg-[#0B0F17] border border-slate-800 rounded-xl p-3.5 group">
                            <p class="text-xs text-slate-200 whitespace-pre-wrap leading-relaxed" x-text="note.content"></p>
                            <div class="mt-2 text-[10px] text-slate-400 flex items-center justify-between border-t border-slate-800/80 pt-2">
                                <span class="font-medium text-slate-400" x-text="(note.user ? note.user.first_name + ' ' + note.user.last_name : 'Team Member') + ' · ' + formatDate(note.created_at)"></span>
                                <div class="flex gap-2">
                                    <button @click="editNote(note)" class="text-cyan-400 hover:text-cyan-300 font-bold">Edit</button>
                                    <button @click="deleteNote(note.id)" class="text-rose-400 hover:text-rose-300 font-bold">Delete</button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="notes.length === 0" class="text-center text-slate-500 text-xs py-6">
                        No notes added yet for this client domain.
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end pt-4 mt-5 border-t border-slate-800">
                    <button type="button" @click="closeNotes()" class="btn-cyber-secondary">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
