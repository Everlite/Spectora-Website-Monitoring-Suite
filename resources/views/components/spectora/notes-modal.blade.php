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

        <!-- shadcn Dialog Panel -->
        <div x-show="isNotesOpen" 
             x-transition:enter="ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" 
             x-transition:leave="ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
             class="inline-block align-bottom bg-card border border-border rounded-xl text-left overflow-hidden shadow-shadcn-lg transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            
            <div class="p-6 space-y-4">
                <!-- Header -->
                <div class="flex items-center justify-between pb-3 border-b border-border">
                    <div class="space-y-0.5">
                        <h3 class="text-base font-semibold text-foreground">Team-Notizen</h3>
                        <p class="text-xs text-muted-foreground font-mono" x-text="domainUrl"></p>
                    </div>
                    <button type="button" @click="closeNotes()" class="text-muted-foreground hover:text-foreground p-1 rounded-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Add Note Input -->
                <div class="space-y-2">
                    <textarea x-model="newNote" 
                              rows="3" 
                              placeholder="Notiz für das Team (Deployments, DNS-Änderungen, Wartungsfenster)..."
                              class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-xs shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"></textarea>
                    <div class="flex justify-end">
                        <button type="button" 
                                @click="addNote()" 
                                :disabled="!newNote.trim()" 
                                class="btn-default disabled:opacity-40">
                            Notiz speichern
                        </button>
                    </div>
                </div>

                <!-- Notes Stream -->
                <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                    <template x-for="note in notes" :key="note.id">
                        <div class="rounded-lg border border-border bg-zinc-950 p-3 space-y-1.5">
                            <p class="text-xs text-foreground whitespace-pre-wrap leading-relaxed" x-text="note.content"></p>
                            <div class="text-[10px] text-muted-foreground flex items-center justify-between border-t border-border/60 pt-1.5">
                                <span x-text="(note.user ? note.user.first_name + ' ' + note.user.last_name : 'Team') + ' · ' + formatDate(note.created_at)"></span>
                                <div class="flex gap-2">
                                    <button @click="editNote(note)" class="text-foreground hover:underline font-medium">Bearbeiten</button>
                                    <button @click="deleteNote(note.id)" class="text-rose-400 hover:underline font-medium">Löschen</button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="notes.length === 0" class="text-center text-muted-foreground text-xs py-6">
                        Noch keine Notizen hinterlegt.
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end pt-3 border-t border-border mt-4">
                    <button type="button" @click="closeNotes()" class="btn-secondary">
                        Schließen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
