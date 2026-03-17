<div>
    <div class="flex items-center justify-between mb-6">
        <select wire:model.live="period" class="h-10 px-4 border border-rule rounded-card text-sm bg-surface">
            <option value="7">Last 7 days</option>
            <option value="30">Last 30 days</option>
            <option value="90">Last 90 days</option>
        </select>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-surface border border-rule rounded-xl p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Total Searches</p>
            <p class="font-display font-extrabold text-2xl text-ink">{{ number_format($totalSearches) }}</p>
        </div>
        <div class="bg-surface border border-rule rounded-xl p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Unique Terms</p>
            <p class="font-display font-extrabold text-2xl text-ink">{{ number_format($uniqueTerms) }}</p>
        </div>
        <div class="bg-surface border border-rule rounded-xl p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Zero-Result Searches</p>
            <p class="font-display font-extrabold text-2xl text-{{ $zeroResults > 0 ? 'red-600' : 'green-600' }}">{{ number_format($zeroResults) }}</p>
        </div>
    </div>

    {{-- Daily Volume Chart --}}
    @if($dailyVolume->isNotEmpty())
        <div class="bg-surface border border-rule rounded-xl p-6 mb-8">
            <h3 class="font-display font-bold text-sm text-ink mb-4">Search Volume</h3>
            <canvas id="searchVolumeChart" height="120" wire:ignore></canvas>
        </div>

        <script wire:ignore>
            document.addEventListener('livewire:navigated', () => {
                const ctx = document.getElementById('searchVolumeChart');
                if (!ctx) return;
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($dailyVolume->keys()),
                        datasets: [{
                            label: 'Searches',
                            data: @json($dailyVolume->values()),
                            borderColor: '#5046e5',
                            backgroundColor: 'rgba(80,70,229,0.1)',
                            fill: true,
                            tension: 0.3,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });
            });
        </script>
    @endif

    {{-- Popular Terms Table --}}
    <div class="bg-surface border border-rule rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-rule">
            <h3 class="font-display font-bold text-sm text-ink">Popular Search Terms</h3>
        </div>
        <table class="w-full">
            <thead>
                <tr class="border-b border-rule text-[11px] font-bold uppercase tracking-widest text-ink3">
                    <th class="text-left px-6 py-3">#</th>
                    <th class="text-left px-6 py-3">Term</th>
                    <th class="text-right px-6 py-3">Searches</th>
                    <th class="text-right px-6 py-3">Avg. Results</th>
                </tr>
            </thead>
            <tbody>
                @forelse($popular as $i => $row)
                    <tr class="border-b border-rule last:border-0 hover:bg-bg/50">
                        <td class="px-6 py-3 text-sm text-ink3">{{ $i + 1 }}</td>
                        <td class="px-6 py-3 text-sm text-ink font-medium">{{ $row->term }}</td>
                        <td class="px-6 py-3 text-sm text-ink text-right">{{ number_format($row->count) }}</td>
                        <td class="px-6 py-3 text-sm text-right {{ $row->avg_results == 0 ? 'text-red-600 font-medium' : 'text-ink3' }}">{{ $row->avg_results }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-8 text-center text-sm text-ink3">No search data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
