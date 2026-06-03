            <!-- Tab Content: Notes -->
            <div x-show="tab === 'notes'" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-6">
                            <div class="bg-spectora-card border border-gray-700/50 rounded-xl p-6 shadow-xl"
                                x-data="notesManager('{{ $domain->uuid }}')">
                                <h3 class="text-lg font-bold text-white mb-4">Notes</h3>

                                <!-- Add Note -->
                                <div class="mb-6">
                                    <textarea x-model="newNote"
                                        class="w-full bg-gray-900 border border-gray-700 rounded text-white text-sm p-3 focus:border-spectora-cyan focus:ring-0"
                                        rows="3" placeholder="Add a new note..."></textarea>
                                    <div class="mt-2 flex justify-end">
                                        <button @click="addNote()" :disabled="!newNote.trim()"
                                            class="bg-spectora-cyan hover:bg-cyan-500 text-white text-sm font-bold py-2 px-4 rounded disabled:opacity-50 transition">
                                            Add Note
                                        </button>
                                    </div>
                                </div>

                                <!-- Notes List -->
                                <div class="space-y-4">
                                    <template x-for="note in notes" :key="note.id">
                                        <div class="bg-gray-800/50 border border-gray-700 rounded-lg p-4 relative group">
                                            <p class="text-gray-300 text-sm whitespace-pre-wrap" x-text="note.content"></p>
                                            <div class="mt-2 flex justify-between items-center text-xs text-gray-500">
                                                <span>
                                                    <span x-text="new Date(note.created_at).toLocaleString()"></span>
                                                    <span x-show="note.author_name" x-text="' · ' + note.author_name"></span>
                                                </span>
                                                <div class="flex gap-2">
                                                    <button @click="editNote(note)"
                                                        class="text-blue-400 hover:text-blue-300 font-bold">Edit</button>
                                                    <button @click="confirmDelete(note.id)"
                                                        class="text-red-400 hover:text-red-300 font-bold">Delete</button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <div x-show="notes.length === 0" class="text-center text-gray-500 py-8">
                                        No notes yet.
                                    </div>
                                </div>

                                <!-- Delete Confirmation Modal -->
                                <div x-show="isDeleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
                                    aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeDeleteModal()"></div>
                                        <div class="inline-block align-bottom bg-gray-800 border border-gray-700 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                            <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-900/50 sm:mx-0 sm:h-10 sm:w-10">
                                                        <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    </div>
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                        <h3 class="text-lg leading-6 font-medium text-white">Are you sure?</h3>
                                                        <div class="mt-2">
                                                            <p class="text-sm text-gray-400">Do you really want to delete this note? This process cannot be undone.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700">
                                                <button type="button" @click="submitDelete()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Delete</button>
                                                <button type="button" @click="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-white hover:bg-gray-600 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Note Modal -->
                                <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeEditModal()"></div>
                                        <div class="inline-block align-bottom bg-gray-800 border border-gray-700 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                            <div class="bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <h3 class="text-lg leading-6 font-medium text-white mb-4">Edit Note</h3>
                                                <textarea x-model="editingContent" class="w-full bg-gray-900 border border-gray-700 rounded text-white text-sm p-3 focus:border-spectora-cyan focus:ring-0" rows="5"></textarea>
                                            </div>
                                            <div class="bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-700">
                                                <button type="button" @click="submitEdit()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-spectora-cyan text-base font-medium text-white hover:bg-cyan-500 sm:ml-3 sm:w-auto sm:text-sm">Save Changes</button>
                                                <button type="button" @click="closeEditModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-600 shadow-sm px-4 py-2 bg-gray-700 text-base font-medium text-white hover:bg-gray-600 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
            </div>
