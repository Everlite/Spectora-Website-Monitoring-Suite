    <!-- Chart.js & Alpine Initialization (Spectora Studio) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        // Spectora Studio Chart Theme Defaults
        if (window.Chart) {
            Chart.defaults.color = '#8A95A8';
            Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
            Chart.defaults.font.size = 11;
            Chart.defaults.plugins.legend.display = false;
        }

        document.addEventListener('DOMContentLoaded', () => {
            // 1. Performance Sparkline
            const perfCtx = document.getElementById('performanceSparkline');
            if (perfCtx) {
                new Chart(perfCtx, {
                    type: 'line',
                    data: {
                        labels: @json($psHistoryLabels ?? ['7d', '6d', '5d', '4d', '3d', '2d', 'Heute']),
                        datasets: [{
                            data: @json($psHistoryScores ?? [$score ?? 0]),
                            borderColor: '#3B57E8',
                            backgroundColor: 'rgba(59, 87, 232, 0.12)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
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

            // 2. Uptime Sparkline
            const uptimeCtx = document.getElementById('uptimeSparkline');
            if (uptimeCtx) {
                new Chart(uptimeCtx, {
                    type: 'line',
                    data: {
                        labels: ['30d', '25d', '20d', '15d', '10d', '5d', 'Heute'],
                        datasets: [{
                            data: @json($uptimeHistory ?? [100, 100, 100, 100, 100, 100, $uptime ?? 100]),
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.12)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { display: false },
                            y: { display: false, min: 90, max: 100 }
                        }
                    }
                });
            }

            // 3. Response Sparkline
            const respCtx = document.getElementById('responseSparkline');
            if (respCtx) {
                new Chart(respCtx, {
                    type: 'line',
                    data: {
                        labels: @json($historyLabels ?? []),
                        datasets: [{
                            data: @json($historyResponseTimes ?? []),
                            borderColor: '#3B57E8',
                            backgroundColor: 'rgba(59, 87, 232, 0.12)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { display: false },
                            y: { display: false }
                        }
                    }
                });
            }

            // 4. Device Pie Chart
            const deviceCtx = document.getElementById('overviewDeviceChart');
            if (deviceCtx) {
                new Chart(deviceCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Desktop', 'Mobile', 'Tablet'],
                        datasets: [{
                            data: [
                                {{ $deviceStats['desktop'] ?? 70 }},
                                {{ $deviceStats['mobile'] ?? 25 }},
                                {{ $deviceStats['tablet'] ?? 5 }}
                            ],
                            backgroundColor: ['#3B57E8', '#10B981', '#F59E0B'],
                            borderWidth: 2,
                            borderColor: '#111622',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.label + ': ' + context.raw + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 5. Traffic Line Chart
            const overviewCtx = document.getElementById('overviewChart');
            if (overviewCtx) {
                new Chart(overviewCtx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels ?? []),
                        datasets: [
                            {
                                label: 'Besucher',
                                data: @json($chartVisitors ?? []),
                                borderColor: '#3B57E8',
                                backgroundColor: 'rgba(59, 87, 232, 0.12)',
                                fill: true,
                                tension: 0.35,
                                borderWidth: 2,
                                pointRadius: 2,
                                pointHoverRadius: 5,
                            },
                            {
                                label: 'Pageviews',
                                data: @json($chartPageviews ?? []),
                                borderColor: '#10B981',
                                backgroundColor: 'transparent',
                                borderDash: [4, 4],
                                tension: 0.35,
                                borderWidth: 1.5,
                                pointRadius: 1,
                                pointHoverRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#111622',
                                borderColor: '#202A3E',
                                borderWidth: 1,
                                padding: 10,
                                titleColor: '#F1F3F9',
                                bodyColor: '#8A95A8',
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false, color: '#202A3E' },
                                ticks: { maxTicksLimit: 8, color: '#8A95A8' }
                            },
                            y: {
                                grid: { color: 'rgba(32, 42, 62, 0.6)' },
                                ticks: { precision: 0, color: '#8A95A8' }
                            }
                        }
                    }
                });
            }

            // 6. History Full Chart
            const historyCtx = document.getElementById('historyChart');
            if (historyCtx) {
                new Chart(historyCtx, {
                    type: 'line',
                    data: {
                        labels: @json($historyLabels ?? []),
                        datasets: [{
                            label: 'Latenz (ms)',
                            data: @json($historyResponseTimes ?? []),
                            borderColor: '#3B57E8',
                            backgroundColor: 'rgba(59, 87, 232, 0.12)',
                            fill: true,
                            tension: 0.3,
                            borderWidth: 2,
                            pointRadius: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ' ' + ctx.raw + ' ms'
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { maxTicksLimit: 10, color: '#8A95A8' }
                            },
                            y: {
                                grid: { color: 'rgba(32, 42, 62, 0.6)' },
                                ticks: { color: '#8A95A8' }
                            }
                        }
                    }
                });
            }
        });

        // Alpine Monitoring Manager
        function monitoringManager() {
            return {
                settings: {
                    only_check_public_pages: {{ $domain->only_check_public_pages ? 'true' : 'false' }},
                    respect_robots_txt: {{ $domain->respect_robots_txt ? 'true' : 'false' }},
                    respect_noindex: {{ $domain->respect_noindex ? 'true' : 'false' }}
                },
                sitemapUrl: '',
                isCrawling: false,

                async crawlSitemap() {
                    if (!this.sitemapUrl) return;
                    this.isCrawling = true;
                    try {
                        const response = await fetch('{{ route('domains.sitemaps.detect', $domain) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ sitemap_url: this.sitemapUrl })
                        });
                        const data = await response.json();
                        alert(data.message || 'Sitemap erfolgreich eingelesen.');
                    } catch (e) {
                        alert('Fehler beim Crawlen der Sitemap.');
                    } finally {
                        this.isCrawling = false;
                    }
                }
            };
        }
    </script>
