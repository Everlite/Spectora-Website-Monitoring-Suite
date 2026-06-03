            <!-- Tab Content: Analytics -->
            <div x-show="tab === 'analytics'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="space-y-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Top Pages -->
                            <div class="bg-spectora-card border border-gray-700/50 rounded-xl p-6 shadow-xl">
                                <h3 class="text-lg font-bold text-white mb-4">Top Pages</h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="text-gray-500 text-xs uppercase tracking-wider border-b border-gray-700">
                                                <th class="pb-3">URL</th>
                                                <th class="pb-3 text-right">Views</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-700/50">
                                            @foreach ($topPages as $page)
                                                <tr class="group hover:bg-gray-800/30 transition">
                                                    <td class="py-3 text-gray-300 font-mono text-sm truncate max-w-xs group-hover:text-white">
                                                        {{ $page->url }}</td>
                                                    <td class="py-3 text-right text-white font-bold">
                                                        {{ number_format($page->total) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Top Sources -->
                            <div class="bg-spectora-card border border-gray-700/50 rounded-xl p-6 shadow-xl">
                                <h3 class="text-lg font-bold text-white mb-4">Top Sources</h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left">
                                        <thead>
                                            <tr class="text-gray-500 text-xs uppercase tracking-wider border-b border-gray-700">
                                                <th class="pb-3">Source</th>
                                                <th class="pb-3 text-right">Visitors</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-700/50">
                                            @foreach ($topSources as $source)
                                                <tr class="group hover:bg-gray-800/30 transition">
                                                    <td class="py-3 text-gray-300 font-mono text-sm truncate max-w-xs group-hover:text-white">
                                                        {{ $source->referrer_domain ?: 'Direct / Unknown' }}</td>
                                                    <td class="py-3 text-right text-white font-bold">
                                                        {{ number_format($source->total) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Top Countries -->
                            <div class="bg-spectora-card border border-gray-700/50 rounded-xl p-6 shadow-xl">
                                <h3 class="text-lg font-bold text-white mb-4">Top Countries</h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm">
                                        <tbody class="divide-y divide-gray-700/50">
                                            @forelse ($topCountries ?? [] as $row)
                                                <tr>
                                                    <td class="py-2 text-gray-300">{{ $row->country }}</td>
                                                    <td class="py-2 text-right text-white font-bold">{{ number_format($row->total) }}</td>
                                                </tr>
                                            @empty
                                                <tr><td class="py-2 text-gray-500" colspan="2">No geo data yet</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Top Cities -->
                            <div class="bg-spectora-card border border-gray-700/50 rounded-xl p-6 shadow-xl">
                                <h3 class="text-lg font-bold text-white mb-4">Top Cities</h3>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm">
                                        <tbody class="divide-y divide-gray-700/50">
                                            @forelse ($topCities ?? [] as $row)
                                                <tr>
                                                    <td class="py-2 text-gray-300">{{ $row->city }}@if($row->country) ({{ $row->country }})@endif</td>
                                                    <td class="py-2 text-right text-white font-bold">{{ number_format($row->total) }}</td>
                                                </tr>
                                            @empty
                                                <tr><td class="py-2 text-gray-500" colspan="2">No city data yet</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Devices -->
                            <div class="bg-spectora-card border border-gray-700/50 rounded-xl p-6 shadow-xl">
                                <h3 class="text-lg font-bold text-white mb-4">Devices</h3>
                                <div class="h-64 relative">
                                    <canvas id="analyticsDeviceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
