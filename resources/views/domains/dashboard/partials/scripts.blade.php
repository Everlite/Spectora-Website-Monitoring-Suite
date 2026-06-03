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
                                    borderColor: '{{ $score >= 90 ? "#10b981" : ($score >= 50 ? "#f59e0b" : "#ef4444") }}',
                                    backgroundColor: '{{ $score >= 90 ? "rgba(16,185,129,0.1)" : ($score >= 50 ? "rgba(245,158,11,0.1)" : "rgba(239,68,68,0.1)") }}',
                                    borderWidth: 2,
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
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16,185,129,0.1)',
                                    borderWidth: 2,
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
                                    borderColor: '{{ $avgResponseTime < 300 ? "#8b5cf6" : "#f59e0b" }}',
                                    backgroundColor: '{{ $avgResponseTime < 300 ? "rgba(139,92,246,0.1)" : "rgba(245,158,11,0.1)" }}',
                                    borderWidth: 2,
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
                                    label: 'Visitors',
                                    data: @json($chartVisitors),
                                    borderColor: '#8b5cf6',
                                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 4
                                },
                                {
                                    label: 'Pageviews',
                                    data: @json($chartPageviews),
                                    borderColor: '#06b6d4',
                                    backgroundColor: 'rgba(6, 182, 212, 0.05)',
                                    borderWidth: 2,
                                    tension: 0.4,
                                    fill: true,
                                    pointRadius: 0,
                                    pointHoverRadius: 4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: 'rgba(148, 163, 184, 0.1)' },
                                    ticks: { color: '#94a3b8', font: { size: 11 } }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { color: '#94a3b8', font: { size: 11 } }
                                }
                            },
                            interaction: {
                                mode: 'index',
                                intersect: false
                            }
                        }
                    });
                },
                initDeviceChart(chartId = 'overviewDeviceChart') {
                    const ctx = document.getElementById(chartId);
                    if (!ctx) return;
                    new Chart(ctx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Desktop', 'Mobile', 'Tablet'],
                            datasets: [{
                                data: @json($deviceData),
                                backgroundColor: [
                                    '#8b5cf6', // Violet (Desktop)
                                    '#06b6d4', // Cyan (Mobile)
                                    '#f59e0b'  // Amber (Tablet)
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            cutout: '65%'
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
