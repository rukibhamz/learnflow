@extends('layouts.admin')

@section('title', 'Overview')

@section('content')
@php
    use App\Models\Order;
    use App\Models\User;
    use App\Models\Enrollment;
    use App\Models\Course;
    use Illuminate\Support\Carbon;

    // KPI values
    $totalRevenue  = Order::paid()->sum('amount') ?? 0;
    $activeStudents = User::role('student')->count();
    $enrolmentsToday = Enrollment::whereDate('created_at', today())->count();

    // Revenue chart — last 30 days grouped by day
    $revenueData = Order::paid()
        ->where('created_at', '>=', now()->subDays(29))
        ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as day, SUM(amount) as total")
        ->groupBy('day')
        ->orderBy('day')
        ->pluck('total', 'day');

    $revenueDays = collect();
    for ($i = 29; $i >= 0; $i--) {
        $day = now()->subDays($i)->format('Y-m-d');
        $revenueDays[$day] = $revenueData[$day] ?? 0;
    }
    $revenueLabels = $revenueDays->keys()->map(fn($d) => Carbon::parse($d)->format('M j'))->values();
    $revenueValues = $revenueDays->values();

    // Enrolments chart — last 12 weeks
    $enrolData = Enrollment::where('created_at', '>=', now()->subWeeks(11)->startOfWeek())
        ->selectRaw("DATE_FORMAT(created_at, '%x-%v') as week, COUNT(*) as total")
        ->groupBy('week')
        ->orderBy('week')
        ->pluck('total', 'week');

    $enrolWeeks = collect();
    for ($i = 11; $i >= 0; $i--) {
        $key = now()->subWeeks($i)->format('Y-W');
        $label = 'Wk ' . now()->subWeeks($i)->format('W');
        $enrolWeeks[$label] = $enrolData[$key] ?? 0;
    }
    $enrolLabels = $enrolWeeks->keys()->values();
    $enrolValues = $enrolWeeks->values();

    // Top courses
    $topCourses = Course::withCount('enrollments')
        ->withSum(['orders' => fn($q) => $q->where('status', 'paid')], 'amount')
        ->orderBy('enrollments_count', 'desc')
        ->take(5)
        ->get();

    // Recent orders
    $recentOrders = Order::with('user', 'course')->latest()->take(8)->get();
@endphp

<div class="space-y-8">

    {{-- Page heading --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="font-poppins font-bold text-2xl tracking-tight text-ink">Overview</h1>
            <p class="text-sm text-ink3 mt-1">Welcome back, {{ auth()->user()->name }}. Here's what's happening.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.analytics.export', ['type' => 'revenue', 'days' => 90]) }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium border border-rule rounded-lg hover:bg-bg transition-colors">
                <span class="material-symbols-outlined text-[16px]">download</span> Revenue CSV
            </a>
            <a href="{{ route('admin.analytics.export', ['type' => 'enrollments', 'days' => 90]) }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium border border-rule rounded-lg hover:bg-bg transition-colors">
                <span class="material-symbols-outlined text-[16px]">download</span> Enrollments CSV
            </a>
        </div>
    </div>

    {{-- KPI row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-surface border border-rule p-6">
            <p class="text-[10px] font-poppins font-bold uppercase tracking-widest text-ink3 mb-2">Total Revenue</p>
            <p class="font-poppins font-extrabold text-2xl tracking-tight text-ink">{{ format_price($totalRevenue) }}</p>
            <p class="text-[10px] mt-2 font-bold text-green-600 uppercase tracking-tighter">All time · paid orders</p>
        </div>
        <div class="bg-surface border border-rule p-6">
            <p class="text-[10px] font-poppins font-bold uppercase tracking-widest text-ink3 mb-2">Active Students</p>
            <p class="font-poppins font-extrabold text-2xl tracking-tight text-ink">{{ number_format($activeStudents) }}</p>
            <p class="text-[10px] mt-2 font-bold text-ink3 uppercase tracking-tighter">Registered accounts</p>
        </div>
        <div class="bg-surface border border-rule p-6">
            <p class="text-[10px] font-poppins font-bold uppercase tracking-widest text-ink3 mb-2">Enrolments Today</p>
            <p class="font-poppins font-extrabold text-2xl tracking-tight text-ink">{{ number_format($enrolmentsToday) }}</p>
            <p class="text-[10px] mt-2 font-bold text-ink3 uppercase tracking-tighter">Since midnight</p>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-12 gap-6">

        {{-- Revenue line chart --}}
        <div class="col-span-12 lg:col-span-8 bg-surface border border-rule p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="font-poppins font-bold text-base tracking-tight text-ink">Revenue</h2>
                    <p class="text-[11px] text-ink3 mt-0.5">Last 30 days · paid orders</p>
                </div>
                <span class="text-[11px] font-poppins font-bold text-primary">{{ format_price($revenueValues->sum()) }}</span>
            </div>
            <div class="h-[220px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Enrolments bar chart --}}
        <div class="col-span-12 lg:col-span-4 bg-surface border border-rule p-6">
            <div class="mb-6">
                <h2 class="font-poppins font-bold text-base tracking-tight text-ink">Enrolments</h2>
                <p class="text-[11px] text-ink3 mt-0.5">Last 12 weeks</p>
            </div>
            <div class="h-[220px]">
                <canvas id="enrolmentsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Bottom row: table + activity --}}
    <div class="grid grid-cols-12 gap-6">

        {{-- Top courses table --}}
        <div class="col-span-12 lg:col-span-8 bg-surface border border-rule overflow-hidden">
            <div class="px-6 py-4 border-b border-rule flex items-center justify-between">
                <h2 class="font-poppins font-bold text-base tracking-tight text-ink">Top Performing Courses</h2>
                <a href="{{ route('admin.courses.index') }}" class="text-[10px] font-bold uppercase tracking-widest text-primary hover:opacity-75 transition-opacity">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-background-light text-[10px] font-poppins font-bold uppercase tracking-widest text-ink3">
                        <tr>
                            <th class="px-6 py-3">Course</th>
                            <th class="px-6 py-3">Instructor</th>
                            <th class="px-6 py-3 text-right">Students</th>
                            <th class="px-6 py-3 text-right">Earnings</th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px]">
                        @forelse($topCourses as $course)
                        <tr class="border-t border-rule hover:bg-background-light/40 transition-colors">
                            <td class="px-6 py-3 font-semibold text-ink">{{ $course->title }}</td>
                            <td class="px-6 py-3 text-ink2">{{ $course->instructor->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-right text-ink2">{{ number_format($course->enrollments_count) }}</td>
                            <td class="px-6 py-3 text-right font-poppins font-bold text-primary">{{ format_price($course->orders_sum_amount ?? 0) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-ink3">No courses yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent activity feed --}}
        <div class="col-span-12 lg:col-span-4 bg-surface border border-rule p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-poppins font-bold text-base tracking-tight text-ink">Recent Orders</h2>
                <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-widest text-ink3">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    Live
                </span>
            </div>
            <div class="space-y-5">
                @forelse($recentOrders as $order)
                <div class="relative pl-5 border-l-2 {{ $loop->first ? 'border-primary' : 'border-rule' }}">
                    <p class="text-[13px] font-semibold text-ink leading-tight">{{ $order->user->name ?? 'Guest' }}</p>
                    <p class="text-[11px] text-ink2 mt-0.5 truncate">{{ optional($order->course)->title ?? '—' }}</p>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-[11px] font-poppins font-bold text-primary">{{ format_price($order->amount) }}</span>
                        <span class="text-[10px] text-ink3">{{ $order->created_at->diffForHumans(null, true) }}</span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-ink3 text-center py-6">No orders yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initRevenueChart('revenueChart',
            {!! $revenueLabels->toJson() !!},
            {!! $revenueValues->toJson() !!},
            {!! json_encode(currency_symbol()) !!}
        );
        initEnrolmentsChart('enrolmentsChart',
            {!! $enrolLabels->toJson() !!},
            {!! $enrolValues->toJson() !!}
        );
    });
</script>
@endpush
@endsection
