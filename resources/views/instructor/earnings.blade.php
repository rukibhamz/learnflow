@extends('layouts.dashboard')

@section('title', 'Earnings')

@prepend('sidebar')
    @php
        $instructorNav = [
            ['label' => 'Overview', 'url' => route('instructor.dashboard'), 'match' => 'instructor/dashboard'],
            ['label' => 'Courses', 'url' => route('instructor.courses.index'), 'match' => 'instructor/courses*'],
            ['label' => 'Earnings', 'url' => route('instructor.earnings'), 'match' => 'instructor/earnings*'],
        ];
    @endphp

    @foreach($instructorNav as $item)
        <a href="{{ $item['url'] }}"
           class="flex items-center px-4 py-2.5 text-[13px] font-medium transition-all duration-150 {{ request()->is($item['match']) ? 'bg-accent-bg text-accent border-r-2 border-accent' : 'text-ink2 hover:bg-bg hover:text-ink' }}">
            {{ $item['label'] }}
        </a>
    @endforeach
@endprepend

@section('content')
@php
    use App\Models\Order;
    use App\Models\Course;
    use Illuminate\Support\Carbon;

    $instructor = auth()->user();
    $courseIds = Course::where('instructor_id', $instructor->id)->pluck('id');

    $totalRevenue = Order::whereIn('course_id', $courseIds)->paid()->sum('amount');
    $thisMonthRevenue = Order::whereIn('course_id', $courseIds)->paid()
        ->where('created_at', '>=', now()->startOfMonth())->sum('amount');
    $lastMonthRevenue = Order::whereIn('course_id', $courseIds)->paid()
        ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
        ->sum('amount');
    $monthGrowth = $lastMonthRevenue > 0 ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100) : 0;

    $revenueData = Order::whereIn('course_id', $courseIds)->paid()
        ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
        ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total")
        ->groupBy('month')->orderBy('month')
        ->pluck('total', 'month');

    $chartMonths = collect();
    for ($i = 5; $i >= 0; $i--) {
        $key = now()->subMonths($i)->format('Y-m');
        $label = now()->subMonths($i)->format('M Y');
        $chartMonths[$label] = (float) ($revenueData[$key] ?? 0);
    }

    $recentOrders = Order::whereIn('course_id', $courseIds)->paid()
        ->with(['user', 'course'])
        ->latest()
        ->take(10)
        ->get();

    $courseSales = Course::where('instructor_id', $instructor->id)
        ->withCount(['orders as paid_orders_count' => fn ($q) => $q->where('status', 'paid')])
        ->withSum(['orders as paid_orders_sum' => fn ($q) => $q->where('status', 'paid')], 'amount')
        ->orderByDesc('paid_orders_sum')
        ->take(5)
        ->get();
@endphp

<div class="max-w-5xl mx-auto">
    <div class="mb-10">
        <h1 class="font-display font-extrabold text-2xl text-ink">Earnings</h1>
        <p class="text-[13px] font-body text-ink2 mt-1">Track your revenue and sales.</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-surface border border-rule rounded-card p-6">
            <p class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Total Revenue</p>
            <p class="font-display font-extrabold text-2xl text-ink">${{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <p class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">This Month</p>
            <p class="font-display font-extrabold text-2xl text-ink">${{ number_format($thisMonthRevenue, 2) }}</p>
            @if($monthGrowth !== 0)
                <p class="text-[11px] mt-1 font-medium {{ $monthGrowth > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $monthGrowth > 0 ? '+' : '' }}{{ $monthGrowth }}% vs last month
                </p>
            @endif
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <p class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Total Sales</p>
            <p class="font-display font-extrabold text-2xl text-ink">{{ $recentOrders->count() > 0 ? Order::whereIn('course_id', $courseIds)->paid()->count() : 0 }}</p>
        </div>
    </div>

    {{-- Revenue Chart --}}
    <div class="bg-surface border border-rule rounded-card p-8 mb-10">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Monthly Revenue</h3>
            <span class="text-[11px] font-display font-bold text-primary">Last 6 months</span>
        </div>
        <div class="h-[240px]">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>

    {{-- Course Sales Breakdown --}}
    @if($courseSales->isNotEmpty())
    <div class="bg-surface border border-rule rounded-card overflow-hidden mb-10">
        <div class="p-6 border-b border-rule">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Sales by Course</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-bg border-b border-rule">
                <tr>
                    <th class="text-left px-6 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Course</th>
                    <th class="text-right px-6 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Sales</th>
                    <th class="text-right px-6 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule">
                @foreach($courseSales as $course)
                <tr class="hover:bg-bg transition-colors">
                    <td class="px-6 py-3 font-medium text-ink">{{ $course->title }}</td>
                    <td class="px-6 py-3 text-right text-ink2">{{ $course->paid_orders_count ?? 0 }}</td>
                    <td class="px-6 py-3 text-right font-display font-bold text-primary">${{ number_format($course->paid_orders_sum ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Recent Sales --}}
    <div class="bg-surface border border-rule rounded-card overflow-hidden">
        <div class="p-6 border-b border-rule">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Recent Sales</h3>
        </div>
        <div class="divide-y divide-rule">
            @forelse($recentOrders as $order)
            <div class="px-6 py-4 flex items-center justify-between hover:bg-bg transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full bg-bg border border-rule flex items-center justify-center font-display font-bold text-ink3 text-[11px]">
                        {{ strtoupper(substr($order->user->name ?? '?', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-[13px] font-bold text-ink">{{ $order->user->name ?? 'Unknown' }}</p>
                        <p class="text-[11px] text-ink3">{{ $order->course->title ?? '—' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="font-display font-bold text-sm text-ink">${{ number_format($order->amount, 2) }}</span>
                    <p class="text-[10px] text-ink3">{{ $order->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="px-6 py-10 text-center text-ink3 text-sm">No sales yet. Create a course to get started!</div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('earningsChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! collect($chartMonths->keys())->toJson() !!},
            datasets: [{
                data: {!! collect($chartMonths->values())->toJson() !!},
                backgroundColor: (getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim() || '#6366f1') + '33',
                borderColor: getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim() || '#6366f1',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: { beginAtZero: true, grid: { color: '#f0f0f0' }, ticks: { font: { size: 10 }, callback: v => '$' + v } }
            }
        }
    });
});
</script>
@endpush
@endsection
