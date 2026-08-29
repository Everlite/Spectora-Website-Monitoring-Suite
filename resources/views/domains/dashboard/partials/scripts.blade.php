    <script src="/js/chart.min.js"></script>
    <script>
        function headerActions() {
            return {
                showTrackingModal: false,
                isAnalyzing: false,
                analysisResult: null,
                analysisError: '',
                
                async runAnalysis() {
                    this.isAnalyzing = true;
                    this.analysisResult = null;
                    this.analysisError = '';
                    
                    try {
                        const response = await fetch('{{ route('domains.analyze', $domain) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            this.analysisResult = 'success';
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            this.analysisResult = 'error';
                            const data = await response.json().catch(() => ({}));
                            this.analysisError = data.message || 'An error occurred.';
                            setTimeout(() => { this.analysisResult = null; }, 5000);
                        }
                    } catch (e) {
                        this.analysisResult = 'error';
                        this.analysisError = e.message;
                        setTimeout(() => { this.analysisResult = null; }, 5000);
                    } finally {
                        this.isAnalyzing = false;
                    }
                }
            }
        }

        function dashboardData() {
            return {
                tab: 'overview', // Default to overview
                showSecurityModal: false,
                showTrafficModal: false,
                init() {
                    this.initOverviewChart();
                    this.initDeviceChart('overviewDeviceChart');
                    this.initDeviceChart('analyticsDeviceChart');
                    this.initSparklines();
                },
                initSparklines() {
                    // Performance Sparkline
                    const perfCtx = document.getElementById('performanceSparkline');
                    if (perfCtx) {
                        new Chart(perfCtx, {
                            type: 'line',
                            data: {
                                labels: @json($psHistoryLabels),
                                datasets: [{
                                    data: @json($psHistoryScores),
                                    borderColor: '{{ $score >= 90 ? "#01B574" : ($score >= 50 ? "#FFB547" : "#EE5D50") }}',
                                    backgroundColor: '{{ $score >= 90 ? "rgba(1,181,116,0.15)" : ($score >= 50 ? "rgba(255,181,71,0.15)" : "rgba(238,93,80,0.15)") }}',
                                    borderWidth: 2.5,
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false }, tooltip: { enabled: true } },
                                scales: {
                                    x: { display: false },
                                    y: { display: false, min: 0, max: 100 }
                                }
                            }
                        });
                    }
                    // Uptime Sparkline
                    const uptimeCtx = document.getElementById('uptimeSparkline');
                    if (uptimeCtx) {
                        new Chart(uptimeCtx, {
                            type: 'line',
                            data: {
                                labels: ['', '', '', '', '', '', ''],
                                datasets: [{
                                    data: @json($uptimeHistory),
                                    borderColor: '#01B574',
                                    backgroundColor: 'rgba(1, 181, 116, 0.15)',
                                    borderWidth: 2.5,
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    x: { display: false },
                                    y: { display: false, min: 95, max: 100.5 }
                                }
                            }
                        });
                    }
                    // Response Time Sparkline
                    const respCtx = document.getElementById('responseSparkline');
                    if (respCtx) {
                        new Chart(respCtx, {
                            type: 'line',
                            data: {
                                labels: @json($historyLabels),
                                datasets: [{
                                    data: @json($historyResponseTimes),
                                    borderColor: '{{ $avgResponseTime < 300 ? "#7551FF" : "#FFB547" }}',
                                    backgroundColor: '{{ $avgResponseTime < 300 ? "rgba(117,81,255,0.15)" : "rgba(255,181,71,0.15)" }}',
                                    borderWidth: 2.5,
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false }, tooltip: { enabled: true } },
                                scales: {
                                    x: { display: false },
                                    y: { display: false }
                                }
                            }
                        });
                    }
                },
                initOverviewChart() {
                    const ctx = document.getElementById('overviewChart');
                    if (!ctx) return;
                    new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [{
                                    label: 'Besucher',
                                    data: @json($chartVisitors),
                                    borderColor: '#7551FF',
                                    backgroundColor: 'rgba(117, 81, 255, 0.12)',
                                    borderWidth: 2.5,
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 5
                                },
                                {
                                    label: 'Pageviews',
                                    data: @json($chartPageviews),
                                    borderColor: '#01B574',
                                    backgroundColor: 'rgba(1, 181, 116, 0.12)',
                                    borderWidth: 2.5,
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 5
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: '#111C44',
                                    borderColor: '#1B254B',
                                    borderWidth: 1,
                                    titleColor: '#FFFFFF',
                                    bodyColor: '#A3AED0'
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#A3AED0', font: { family: 'Plus Jakarta Sans', size: 10 } }
                                },
                                y: {
                                    grid: { color: '#1B254B' },
                                    ticks: { color: '#A3AED0', font: { family: 'Plus Jakarta Sans', size: 10 } }
                                }
                            }
                        }
                    });
                },
                initDeviceChart(canvasId) {
                    const ctx = document.getElementById(canvasId);
                    if (!ctx) return;
                    new Chart(ctx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Desktop', 'Mobile', 'Tablet'],
                            datasets: [{
                                data: [
                                    {{ $deviceStats['desktop'] ?? 0 }},
                                    {{ $deviceStats['mobile'] ?? 0 }},
                                    {{ $deviceStats['tablet'] ?? 0 }}
                                ],
                                backgroundColor: ['#7551FF', '#01B574', '#FFB547'],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '75%',
                            plugins: { legend: { display: false } }
                        }
                    });
                }
            }
        }

        function historyManager() {
            return {
                isAnalyzing: false,
                analysisResult: null, // 'success' | 'error' | null
                analysisError: '',
                showAllLogs: false,
                
                async runAnalysis() {
                    this.isAnalyzing = true;
                    this.analysisResult = null;
                    this.analysisError = '';
                    
                    try {
                        const response = await fetch('{{ route('domains.analyze', $domain) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            this.analysisResult = 'success';
                            // Reload after 2 seconds to show updated data
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            this.analysisResult = 'error';
                            const data = await response.json().catch(() => ({}));
                            this.analysisError = data.message || 'An unknown error occurred.';
                        }
                    } catch (e) {
                        this.analysisResult = 'error';
                        this.analysisError = 'Network error: ' + e.message;
                    } finally {
                        this.isAnalyzing = false;
                    }
                }
            }
        }

        function notesManager(domainUuid) {
            return {
                notes: @json($notes),
                newNote: '',
                isDeleteModalOpen: false,
                deleteNoteId: null,
                isEditModalOpen: false,
                editingNoteId: null,
                editingContent: '',

                async addNote() {
                    if (!this.newNote.trim()) return;

                    try {
                        const response = await fetch(`/domains/${domainUuid}/notes`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                content: this.newNote
                            })
                        });

                        if (response.ok) {
                            const note = await response.json();
                            this.notes.unshift(note);
                            this.newNote = '';
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                confirmDelete(noteId) {
                    this.deleteNoteId = noteId;
                    this.isDeleteModalOpen = true;
                },

                closeDeleteModal() {
                    this.isDeleteModalOpen = false;
                    this.deleteNoteId = null;
                },

                async submitDelete() {
                    if (!this.deleteNoteId) return;

                    try {
                        const response = await fetch(`/notes/${this.deleteNoteId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) {
                            this.notes = this.notes.filter(n => n.id !== this.deleteNoteId);
                            this.closeDeleteModal();
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                editNote(note) {
                    this.editingNoteId = note.id;
                    this.editingContent = note.content;
                    this.isEditModalOpen = true;
                },

                closeEditModal() {
                    this.isEditModalOpen = false;
                    this.editingNoteId = null;
                    this.editingContent = '';
                },

                async submitEdit() {
                    if (!this.editingNoteId || !this.editingContent.trim()) return;

                    try {
                        const response = await fetch(`/notes/${this.editingNoteId}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                content: this.editingContent
                            })
                        });

                        if (response.ok) {
                            const updatedNote = await response.json();
                            const index = this.notes.findIndex(n => n.id === this.editingNoteId);
                            if (index !== -1) {
                                this.notes[index] = updatedNote;
                            }
                            this.closeEditModal();
                        }
                    } catch (e) {
                        console.error(e);
                    }
                }
            }
        }

        function monitoringManager() {
            return {
                isSaving: false,
                isDetecting: false,
                showUrlModal: false,
                isScanningUrls: false,
                isSyncingUrls: false,
                sitemap_urls: @json($domain->sitemap_urls ?? []),
                monitoredUrls: @json($domain->monitoredUrls),
                discoveredUrls: [],
                allSelected: false,
                settings: {
                    only_check_public_pages: {{ $domain->only_check_public_pages ? 'true' : 'false' }},
                    respect_robots_txt: {{ $domain->respect_robots_txt ? 'true' : 'false' }},
                    respect_noindex: {{ $domain->respect_noindex ? 'true' : 'false' }},
                    exclude_patterns: @json($domain->exclude_patterns ?? ''),
                    included_sitemaps: @json($domain->included_sitemaps ?? []),
                },

                async saveSettings() {
                    this.isSaving = true;
                    try {
                        const response = await fetch('{{ route('domains.settings.update', $domain) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.settings)
                        });
                        if (response.ok) {
                            // Show success state briefly then reload
                            const btn = event.target.closest('button');
                            const originalText = btn.innerText;
                            btn.innerText = 'Saved!';
                            btn.classList.add('bg-emerald-600');
                            
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isSaving = false;
                    }
                },

                async detectSitemaps() {
                    this.isDetecting = true;
                    try {
                        const response = await fetch('{{ route('domains.sitemaps.detect', $domain) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.sitemap_urls = data.sitemaps;
                            this.settings.included_sitemaps = data.sitemaps; // Auto-include new ones?
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isDetecting = false;
                    }
                },

                async openUrlSelector() {
                    this.showUrlModal = true;
                    this.isScanningUrls = true;
                    try {
                        const response = await fetch('{{ route('domains.urls.scan', $domain) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        if (response.ok) {
                            const data = await response.json();
                            this.discoveredUrls = data.urls;
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isScanningUrls = false;
                    }
                },

                toggleAllUrls() {
                    this.allSelected = !this.allSelected;
                    this.discoveredUrls.forEach(u => u.is_monitored = this.allSelected);
                },

                selectPublicOnly() {
                    this.discoveredUrls.forEach(u => u.is_monitored = u.is_public);
                },

                async saveUrlSelection() {
                    this.isSyncingUrls = true;
                    try {
                        const response = await fetch('{{ route('domains.urls.sync', $domain) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ urls: this.discoveredUrls })
                        });
                        if (response.ok) {
                            window.location.reload();
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isSyncingUrls = false;
                    }
                }
            }
        }
    </script>
