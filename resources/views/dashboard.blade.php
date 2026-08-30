<x-app-layout>
    <div class="space-y-6" x-data="dashboardManager()">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-medium text-studio-text">Start</h1>
                <p class="text-sm text-studio-muted mt-0.5">Nutzer heute, dann deine Websites.</p>
            </div>
            <button type="button" @click="$dispatch('open-add-domain')" class="btn-spectora-primary">
                Website hinzufügen
            </button>
        </div>

        @if (session('status'))
            <div class="border border-studio-emerald/30 bg-studio-emerald/10 p-3 text-xs text-studio-emerald">
                {{ session('status') }}
            </div>
        @endif

        <x-spectora.global-metrics :kpis="$kpis ?? []" />

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <input type="text"
                   x-model="searchQuery"
                   placeholder="Suchen…"
                   class="spectora-input max-w-sm">

            <div class="flex flex-wrap items-center gap-5 text-sm">
                <button type="button" @click="filterStatus = 'all'" :class="filterStatus === 'all' ? 'text-studio-text' : 'text-studio-muted hover:text-studio-text'">Alle</button>
                <button type="button" @click="filterStatus = 'online'" :class="filterStatus === 'online' ? 'text-studio-emerald' : 'text-studio-muted hover:text-studio-text'">Erreichbar</button>
                <button type="button" @click="filterStatus = 'offline'" :class="filterStatus === 'offline' ? 'text-studio-rose' : 'text-studio-muted hover:text-studio-text'">Störungen</button>
                <span class="text-studio-subtle">·</span>
                <button type="button" @click="viewMode = 'table'" :class="viewMode === 'table' ? 'text-studio-text' : 'text-studio-muted'">Liste</button>
                <button type="button" @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'text-studio-text' : 'text-studio-muted'">Kacheln</button>
            </div>
        </div>

        <!-- 4. Fleet Data (Table or Grid) -->
        @if(count($domains) > 0)
            <!-- Table View -->
            <div x-show="viewMode === 'table'">
                <x-spectora.domain-table :domains="$domains" />
            </div>

            <!-- Grid View -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach ($domains as $domain)
                    <div x-show="matchesFilter({{ json_encode(strtolower($domain->url)) }}, {{ json_encode($domain->status_code >= 200 && $domain->status_code < 400 ? 'online' : 'offline') }})">
                        <x-spectora.domain-card :domain="$domain" />
                    </div>
                @endforeach
            </div>
        @else
            <!-- Zero State -->
            <div class="spectora-card p-10 text-center">
                <p class="text-lg font-medium">Noch keine Website</p>
                <p class="text-sm text-studio-muted mt-2 mb-5">Website hinzufügen, Pulse-Snippet einbauen. Uptime läuft automatisch.</p>
                <button type="button" @click="$dispatch('open-add-domain')" class="btn-spectora-primary">
                    Website hinzufügen
                </button>
            </div>
        @endif

        <!-- Modals -->
        <x-spectora.add-domain-modal />
        <x-spectora.watchdog-modal />
        <x-spectora.notes-modal />
        <x-spectora.delete-modal />
        <x-spectora.tracking-modal />

    </div>

    <!-- Alpine Dashboard Controller -->
    <script>
        function dashboardManager() {
            return {
                searchQuery: '',
                filterStatus: 'all',
                viewMode: 'table',

                matchesFilter(url, status) {
                    const matchesSearch = !this.searchQuery || url.includes(this.searchQuery.toLowerCase());
                    const matchesStatus = this.filterStatus === 'all' || this.filterStatus === status;
                    return matchesSearch && matchesStatus;
                },

                // Tracking Code Modal
                isTrackingOpen: false,
                trackingDomainUrl: '',
                trackingDomainUuid: '',
                copied: false,

                openTracking(url, uuid) {
                    this.trackingDomainUrl = url;
                    this.trackingDomainUuid = uuid;
                    this.isTrackingOpen = true;
                    this.copied = false;
                },

                closeTracking() {
                    this.isTrackingOpen = false;
                },

                getSnippet() {
                    const host = window.location.origin;
                    return `<script defer src="${host}/js/sp-pulse.js" data-domain="${this.trackingDomainUuid}"><\/script>`;
                },

                copySnippet() {
                    navigator.clipboard.writeText(this.getSnippet());
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2000);
                },

                // Notes Modal
                isNotesOpen: false,
                domainId: null,
                domainUrl: '',
                notes: [],
                newNote: '',

                async openNotes(id, url) {
                    this.domainId = id;
                    this.domainUrl = url;
                    this.isNotesOpen = true;
                    this.newNote = '';
                    await this.fetchNotes();
                },

                closeNotes() {
                    this.isNotesOpen = false;
                    this.domainId = null;
                    this.notes = [];
                },

                async fetchNotes() {
                    try {
                        const response = await fetch(`/domains/${this.domainId}/notes`);
                        if (response.ok) {
                            this.notes = await response.json();
                        }
                    } catch (e) {
                        console.error('Failed to fetch notes:', e);
                    }
                },

                async addNote() {
                    if (!this.newNote.trim()) return;

                    try {
                        const response = await fetch(`/domains/${this.domainId}/notes`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ content: this.newNote })
                        });

                        if (response.ok) {
                            this.newNote = '';
                            await this.fetchNotes();
                        }
                    } catch (e) {
                        console.error('Failed to add note:', e);
                    }
                },

                async editNote(note) {
                    const newContent = prompt('Notiz bearbeiten:', note.content);
                    if (newContent === null || newContent === note.content) return;

                    try {
                        const response = await fetch(`/notes/${note.id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ content: newContent })
                        });

                        if (response.ok) {
                            await this.fetchNotes();
                        }
                    } catch (e) {
                        console.error('Failed to edit note:', e);
                    }
                },

                formatDate(dateString) {
                    try {
                        const d = new Date(dateString);
                        return d.toLocaleDateString('de-DE', {day: '2-digit', month: '2-digit', year: 'numeric'}) + ' ' + d.toLocaleTimeString('de-DE', {hour: '2-digit', minute: '2-digit'});
                    } catch (e) {
                        return dateString;
                    }
                },

                // Watchdog Modal
                isWatchdogOpen: false,
                watchdogUrl: '',
                watchdogDetails: null,
                watchdogType: '',

                openWatchdog(url, details, type) {
                    this.watchdogUrl = url;
                    this.watchdogDetails = details;
                    this.watchdogType = type;
                    this.isWatchdogOpen = true;
                },

                closeWatchdog() {
                    this.isWatchdogOpen = false;
                    this.watchdogDetails = null;
                },

                copyWatchdogJson() {
                    navigator.clipboard.writeText(JSON.stringify(this.watchdogDetails, null, 2));
                },

                // Delete Modal
                isDeleteModalOpen: false,
                deleteType: '',
                deleteLabel: '',
                deleteTargetId: '',

                confirmDelete(type, id, label) {
                    this.deleteType = type;
                    this.deleteTargetId = id;
                    this.deleteLabel = label;
                    this.isDeleteModalOpen = true;
                },

                closeDeleteModal() {
                    this.isDeleteModalOpen = false;
                    this.deleteType = '';
                    this.deleteTargetId = '';
                    this.deleteLabel = '';
                },

                async submitDelete() {
                    if (this.deleteType === 'domain') {
                        const form = document.getElementById(this.deleteTargetId);
                        if (form) form.submit();
                    } else if (this.deleteType === 'note') {
                        await this.performDeleteNote(this.deleteTargetId);
                        this.closeDeleteModal();
                    }
                },

                deleteNote(noteId) {
                    this.confirmDelete('note', noteId, 'diese Notiz');
                },

                async performDeleteNote(noteId) {
                    try {
                        const response = await fetch(`/notes/${noteId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            await this.fetchNotes();
                        }
                    } catch (e) {
                        console.error('Failed to delete note:', e);
                    }
                }
            };
        }

        function addDomainManager() {
            return {
                isOpen: {{ $errors->any() ? 'true' : 'false' }},
                openModal() {
                    this.isOpen = true;
                },
                closeModal() {
                    this.isOpen = false;
                }
            };
        }
    </script>
</x-app-layout>