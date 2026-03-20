@extends('layouts.dashboard')

@section('title', 'Instructor Dashboard')

@section('content')
@php
    use App\Models\Order;
    use App\Models\Enrollment;
    use App\Models\Course;
    use App\Enums\CourseStatus;
    use Illuminate\Support\Carbon;

    $instructor = auth()->user();
    $courseIds = Course::where('instructor_id', $instructor->id)->pluck('id');

    $totalRevenue = Order::whereIn('course_id', $courseIds)->paid()->sum('amount');
    $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)->count();
    $avgRating = \App\Models\Review::whereIn('course_id', $courseIds)
        ->whereNotNull('approved_at')->avg('rating');
    $activeCourses = Course::where('instructor_id', $instructor->id)
        ->where('status', CourseStatus::Published)->count();

    $revenueData = Order::whereIn('course_id', $courseIds)->paid()
        ->where('created_at', '>=', now()->subDays(29))
        ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as day, SUM(amount) as total")
        ->groupBy('day')->orderBy('day')
        ->pluck('total', 'day');

    $revenueDays = collect();
    for ($i = 29; $i >= 0; $i--) {
        $day = now()->subDays($i)->format('Y-m-d');
        $revenueDays[$day] = (float) ($revenueData[$day] ?? 0);
    }
    $revenueLabels = $revenueDays->keys()->map(fn($d) => Carbon::parse($d)->format('M j'))->values();
    $revenueValues = $revenueDays->values();

    $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)
        ->with(['user', 'course'])
        ->latest('created_at')
        ->take(8)
        ->get();
@endphp

<div class="max-w-5xl mx-auto">
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="font-display font-extrabold text-2xl text-ink">Instructor Overview</h1>
            <p class="text-[13px] font-body text-ink2 mt-1">Track your course performance and earnings.</p>
        </div>
        <a href="{{ route('instructor.courses.create') }}" class="px-5 py-2.5 bg-ink text-white font-display font-bold text-[12px] rounded-card hover:opacity-90 transition-opacity">Create Course</a>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Total Revenue</span>
            <span class="font-display font-extrabold text-2xl text-ink">{{ format_price($totalRevenue) }}</span>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Enrollments</span>
            <span class="font-display font-extrabold text-2xl text-ink">{{ number_format($totalEnrollments) }}</span>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Course Rating</span>
            <span class="font-display font-extrabold text-2xl text-ink">{{ $avgRating ? number_format($avgRating, 1) : '—' }}</span>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Active Courses</span>
            <span class="font-display font-extrabold text-2xl text-ink">{{ $activeCourses }}</span>
        </div>
    </div>

    {{-- Revenue Chart --}}
    <div class="bg-surface border border-rule rounded-card p-8 mb-12">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Revenue</h3>
                <p class="text-[11px] text-ink3 mt-0.5">Last 30 days</p>
            </div>
            <span class="text-[11px] font-display font-bold text-primary">{{ format_price($revenueValues->sum()) }}</span>
        </div>
        <div class="h-[220px]">
            <canvas id="instructorRevenueChart"></canvas>
        </div>
    </div>

    {{-- Recent Enrollments --}}
    <div class="bg-surface border border-rule rounded-card overflow-hidden">
        <div class="p-6 border-b border-rule flex items-center justify-between">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Recent Enrollments</h3>
        </div>
        <div class="divide-y divide-rule">
            @forelse($recentEnrollments as $enrollment)
            <div class="p-4 flex items-center justify-between hover:bg-bg transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 rounded-full bg-bg border border-rule flex items-center justify-center font-display font-bold text-ink2 text-[12px]">
                        {{ strtoupper(substr($enrollment->user->name ?? '?', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-[13px] font-bold text-ink leading-none">{{ $enrollment->user->name ?? 'Unknown' }}</p>
                        <p class="text-[11px] text-ink3 mt-1">Enrolled in {{ $enrollment->course->title ?? '—' }}</p>
                    </div>
                </div>
                <span class="text-[11px] font-medium text-ink2">{{ $enrollment->created_at->diffForHumans() }}</span>
            </div>
            @empty
            <div class="p-8 text-center text-ink3 text-sm">No enrollments yet.</div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('instructorRevenueChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! $revenueLabels->toJson() !!},
            datasets: [{
                data: {!! $revenueValues->toJson() !!},
                borderColor: getComputedStyle(document.documentElement).getPropertyValue('--color-primary').trim() || '#6366f1',
                backgroundColor: 'transparent',
                borderWidth: 2,
                tension: 0.3,
                pointRadius: 0,
                pointHitRadius: 10,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 8, font: { size: 10 } } },
                y: { beginAtZero: true, grid: { color: '#f0f0f0' }, ticks: { font: { size: 10 }, callback: v => '{{ addslashes(currency_symbol()) }}' + v } }
            }
        }
    });
});
</script>
@endpush
@endsection
