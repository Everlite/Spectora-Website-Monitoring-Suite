<x-app-layout>
    <div class="space-y-6" x-data="dashboardManager()">
        
        <!-- 1. Top Horizon Bar: Action Header -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">Flotten-Übersicht</h2>
                <p class="text-xs text-[#A3AED0] mt-0.5">
                    Live-Zustand aller überwachten Ziel-Websites, SLA Uptime-Probes und DSGVO-Telemetrie.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" 
                        @click="$dispatch('open-add-domain')" 
                        class="btn-horizon-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>Website hinzufügen</span>
                </button>
            </div>
        </div>

        <!-- Global Flash Messages -->
        @if (session('status'))
            <div class="rounded-horizon bg-[#01B574]/20 border border-[#01B574]/40 p-4 text-xs font-bold text-[#01B574] flex items-center gap-2.5 shadow-sm">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- 2. Horizon 4-Card Circular Metric Grid -->
        <x-spectora.global-metrics :kpis="$kpis ?? []" />

        <!-- 3. Horizon Search & Filter Controls -->
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 pt-2">
            
            <!-- Search Pill -->
            <div class="relative flex-1 max-w-sm">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#A3AED0]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Domains filtern..." 
                       class="horizon-input pl-10">
            </div>

            <!-- Filter Pills & View Switcher -->
            <div class="flex items-center gap-3">
                <!-- Status Pills -->
                <div class="inline-flex rounded-full bg-[#111C44] border border-[#1B254B] p-1 text-[#A3AED0]">
                    <button type="button" 
                            @click="filterStatus = 'all'" 
                            :class="filterStatus === 'all' ? 'bg-[#7551FF] text-white font-bold shadow-horizon-btn' : 'hover:text-white'"
                            class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all">
                        Alle ({{ count($domains) }})
                    </button>
                    <button type="button" 
                            @click="filterStatus = 'online'" 
                            :class="filterStatus === 'online' ? 'bg-[#01B574] text-white font-bold' : 'hover:text-white'"
                            class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all">
                        Online ({{ $kpis['online_count'] ?? 0 }})
                    </button>
                    <button type="button" 
                            @click="filterStatus = 'offline'" 
                            :class="filterStatus === 'offline' ? 'bg-[#EE5D50] text-white font-bold' : 'hover:text-white'"
                            class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all">
                        Störungen ({{ $kpis['active_incidents'] ?? 0 }})
                    </button>
                </div>

                <!-- View Mode Switcher -->
                <div class="inline-flex rounded-full bg-[#111C44] border border-[#1B254B] p-1 text-[#A3AED0]">
                    <button type="button" 
                            @click="viewMode = 'table'" 
                            :class="viewMode === 'table' ? 'bg-[#1B254B] text-white' : 'hover:text-white'"
                            class="p-2 rounded-full transition-all" title="Tabellenansicht">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </button>
                    <button type="button" 
                            @click="viewMode = 'grid'" 
                            :class="viewMode === 'grid' ? 'bg-[#1B254B] text-white' : 'hover:text-white'"
                            class="p-2 rounded-full transition-all" title="Kartenansicht">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- 4. Main Domain List (Horizon Table or Grid) -->
        @if(count($domains) > 0)
            <!-- Table View -->
            <div x-show="viewMode === 'table'">
                <x-spectora.domain-table :domains="$domains" />
            </div>

            <!-- Grid Cards View -->
            <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach ($domains as $domain)
                    <div x-show="matchesFilter({{ json_encode(strtolower($domain->url)) }}, {{ json_encode($domain->status_code >= 200 && $domain->status_code < 400 ? 'online' : 'offline') }})">
                        <x-spectora.domain-card :domain="$domain" />
                    </div>
                @endforeach
            </div>
        @else
            <!-- Zero State -->
            <div class="horizon-card p-12 text-center my-8 max-w-md mx-auto border-dashed">
                <div class="w-14 h-14 rounded-full bg-[#1B254B] text-[#7551FF] flex items-center justify-center mx-auto mb-3 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                </div>
                <h3 class="text-base font-bold text-white">Noch keine Ziel-Websites hinterlegt</h3>
                <p class="text-xs text-[#A3AED0] mt-1 mb-5">Füge deine erste Website hinzu, um Uptime-Probes und Telemetrie zu starten.</p>
                <button type="button" @click="$dispatch('open-add-domain')" class="btn-horizon-primary">
                    + Website hinzufügen
                </button>
            </div>
        @endif

        <!-- Horizon Modals -->
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