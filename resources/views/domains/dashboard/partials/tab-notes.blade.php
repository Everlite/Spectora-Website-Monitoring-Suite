            <!-- Tab Content: Notes -->
            <div x-show="tab === 'notes'" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">
                <div class="horizon-card p-6"
                     x-data="notesManager('{{ $domain->uuid }}')">
                    <h3 class="text-sm font-bold text-white mb-4">Team-Notizen</h3>

                    <!-- Add Note -->
                    <div class="mb-6 space-y-2">
                        <textarea x-model="newNote"
                            class="w-full bg-[#0B1437] border border-[#1B254B] rounded-horizon-sm text-white text-xs p-3.5 placeholder-[#A3AED0] focus:border-[#7551FF] focus:ring-1 focus:ring-[#7551FF]"
                            rows="3" placeholder="Neue Notiz hinzufügen..."></textarea>
                        <div class="flex justify-end">
                            <button @click="addNote()" :disabled="!newNote.trim()"
                                class="btn-horizon-primary disabled:opacity-40">
                                Notiz speichern
                            </button>
                        </div>
                    </div>

                    <!-- Notes List -->
                    <div class="space-y-3">
                        <template x-for="note in notes" :key="note.id">
                            <div class="bg-[#0B1437] border border-[#1B254B] rounded-horizon-sm p-4 relative group">
                                <p class="text-white text-xs whitespace-pre-wrap leading-relaxed" x-text="note.content"></p>
                                <div class="mt-2.5 flex justify-between items-center text-[10px] text-[#A3AED0] border-t border-[#1B254B] pt-2">
                                    <span>
                                        <span x-text="new Date(note.created_at).toLocaleString('de-DE')"></span>
                                        <span x-show="note.author_name" x-text="' · ' + note.author_name"></span>
                                    </span>
                                    <div class="flex gap-3">
                                        <button @click="editNote(note)"
                                            class="text-white hover:text-[#7551FF] font-bold">Bearbeiten</button>
                                        <button @click="confirmDelete(note.id)"
                                            class="text-[#EE5D50] hover:underline font-bold">Löschen</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="notes.length === 0" class="text-center text-[#A3AED0] text-xs py-8">
                            Noch keine Notizen hinterlegt.
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div x-show="isDeleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-[#080F27]/80 backdrop-blur-md transition-opacity" aria-hidden="true" @click="closeDeleteModal()"></div>
                            <div class="inline-block align-bottom bg-[#111C44] border border-[#1B254B] rounded-horizon text-left overflow-hidden shadow-horizon-card transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full p-6 space-y-4">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-10 h-10 rounded-full bg-[#EE5D50]/20 border border-[#EE5D50]/40 flex items-center justify-center shrink-0 text-[#EE5D50]">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="space-y-1">
                                        <h3 class="text-sm font-bold text-white">Notiz löschen?</h3>
                                        <p class="text-xs text-[#A3AED0]">Diese Aktion kann nicht rückgängig gemacht werden.</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#1B254B]">
                                    <button type="button" @click="closeDeleteModal()" class="btn-horizon-secondary">Abbrechen</button>
                                    <button type="button" @click="submitDelete()" class="btn-horizon-danger">Löschen</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Note Modal -->
                    <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-[#080F27]/80 backdrop-blur-md transition-opacity" aria-hidden="true" @click="closeEditModal()"></div>
                            <div class="inline-block align-bottom bg-[#111C44] border border-[#1B254B] rounded-horizon text-left overflow-hidden shadow-horizon-card transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full p-6 space-y-4">
                                <h3 class="text-sm font-bold text-white">Notiz bearbeiten</h3>
                                <textarea x-model="editingContent" class="w-full bg-[#0B1437] border border-[#1B254B] rounded-horizon-sm text-white text-xs p-3.5 focus:border-[#7551FF] focus:ring-1 focus:ring-[#7551FF]" rows="4"></textarea>
                                <div class="flex justify-end gap-3 pt-4 border-t border-[#1B254B]">
                                    <button type="button" @click="closeEditModal()" class="btn-horizon-secondary">Abbrechen</button>
                                    <button type="button" @click="submitEdit()" class="btn-horizon-primary">Speichern</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
