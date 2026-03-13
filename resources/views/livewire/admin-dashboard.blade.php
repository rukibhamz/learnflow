<div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        @foreach([
            ['label' => 'Total revenue', 'value' => '$48,240'],
            ['label' => 'Active students', 'value' => '2,450'],
            ['label' => 'Enrolments today', 'value' => '42'],
            ['label' => 'Completion rate', 'value' => '68%'],
            ['label' => 'Pending review', 'value' => '3'],
            ['label' => 'Active instructors', 'value' => '62'],
        ] as $kpi)
        <div class="bg-surface border border-rule rounded-card p-6">
            <p class="text-[11px] font-body text-ink3 mb-1">{{ $kpi['label'] }}</p>
            <p class="font-display font-extrabold text-2xl text-ink">{{ $kpi['value'] }}</p>
        </div>
        @endforeach
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-display font-bold text-ink">Revenue</h2>
                <div class="flex gap-2" wire:ignore>
                    @foreach(['7d', '30d', '90d', '1y'] as $p)
                        <button class="px-3 py-1 rounded-pill border border-rule text-[12px] font-body {{ $p === '30d' ? 'bg-ink text-white border-ink' : 'text-ink2 hover:border-ink' }}">{{ $p }}</button>
                    @endforeach
                </div>
            </div>
            <div class="bg-surface border border-rule rounded-card p-8 h-64 flex items-center justify-center text-ink3 font-body text-sm">
                Chart — integrate Chart.js
            </div>
            <div class="mt-6 bg-surface border border-rule rounded-card overflow-hidden">
                <h3 class="font-display font-bold text-sm text-ink p-4 border-b border-rule">Top courses</h3>
                <table class="w-full">
                    <tbody>
                        @foreach([['title' => 'Web Dev Bootcamp', 'instructor' => 'Jane Doe', 'enrolments' => 240, 'revenue' => '$11,760', 'completion' => '72%', 'rating' => '4.8']] as $row)
                        <tr class="border-b border-rule h-11">
                            <td class="py-3 px-4 font-body text-[13px]">{{ $row['title'] }}</td>
                            <td class="py-3 px-4 font-body text-[13px] text-ink3">{{ $row['instructor'] }}</td>
                            <td class="py-3 px-4 font-body text-[13px]">{{ $row['enrolments'] }}</td>
                            <td class="py-3 px-4 font-body text-[13px]">{{ $row['revenue'] }}</td>
                            <td class="py-3 px-4 font-body text-[13px]">{{ $row['completion'] }}</td>
                            <td class="py-3 px-4 font-body text-[13px]">★ {{ $row['rating'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div>
            <h3 class="font-display font-bold text-sm text-ink mb-4">Recent orders</h3>
            <div class="bg-surface border border-rule rounded-card divide-y divide-rule max-h-[400px] overflow-y-auto">
                @foreach(range(1, 5) as $i)
                <div class="p-4">
                    <p class="font-body text-[13px] text-ink">Web Dev Bootcamp — $49</p>
                    <p class="text-[11px] text-ink3 mt-1">Just now</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
