@extends('layouts.admin')

@section('title', 'Overview')

@section('content')
<div class="grid grid-cols-12 gap-8">
    <!-- Left Column: KPIs and Chart -->
    <div class="col-span-12 lg:col-span-9 space-y-8">
        <!-- KPI Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $kpis = [
                    ['label' => 'Total revenue', 'value' => '$' . number_format(\App\Models\Order::paid()->sum('amount') ?? 0, 2), 'hint' => '+12.5% vs last month', 'hint_color' => 'text-green-600'],
                    ['label' => 'Active students', 'value' => number_format(\App\Models\User::role('student')->count()), 'hint' => '-2.1% vs last month', 'hint_color' => 'text-red-600'],
                    ['label' => 'Enrolments today', 'value' => number_format(\App\Models\Enrollment::whereDate('created_at', today())->count()), 'hint' => '+4.3% vs yesterday', 'hint_color' => 'text-green-600'],
                ];
            @endphp

            @foreach($kpis as $kpi)
            <div class="p-6 border border-rule bg-surface">
                <p class="text-[10px] font-poppins font-bold uppercase tracking-widest text-ink3 mb-2">{{ $kpi['label'] }}</p>
                <p class="font-poppins font-extrabold text-2xl tracking-tight text-ink">{{ $kpi['value'] }}</p>
                <p class="text-[10px] mt-2 font-bold {{ $kpi['hint_color'] }} uppercase tracking-tighter">{{ $kpi['hint'] }}</p>
            </div>
            @endforeach
        </div>

        <!-- Revenue Chart -->
        <div class="border border-rule bg-surface p-8">
            <div class="flex items-center justify-between mb-10">
                <div class="space-y-1">
                    <h2 class="font-poppins font-bold text-lg tracking-tight text-ink">Revenue Overview</h2>
                    <p class="text-[11px] text-ink3 font-medium">Monthly operational revenue through Stripe</p>
                </div>
                <div class="flex border border-rule rounded-none p-1 bg-background-light">
                    <button class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-ink bg-surface border border-rule">7d</button>
                    <button class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-ink3 hover:text-ink transition-colors">30d</button>
                    <button class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest text-ink3 hover:text-ink transition-colors">90d</button>
                </div>
            </div>
            <div class="h-[280px] w-full relative">
                <!-- Y-Axis Labels -->
                <div class="absolute -left-2 inset-y-0 flex flex-col justify-between text-[10px] text-ink3 font-bold py-2">
                    <span>$10k</span><span>$7.5k</span><span>$5k</span><span>$2.5k</span><span>$0</span>
                </div>
                <!-- Gridlines -->
                <div class="ml-10 h-full flex flex-col justify-between py-2 border-l border-rule">
                    <div class="w-full border-t border-rule/50 border-dashed"></div>
                    <div class="w-full border-t border-rule/50 border-dashed"></div>
                    <div class="w-full border-t border-rule/50 border-dashed"></div>
                    <div class="w-full border-t border-rule/50 border-dashed"></div>
                    <div class="w-full border-t border-rule"></div>
                </div>
                <!-- Path -->
                <svg class="absolute bottom-2 left-10 right-0 h-[200px] w-[calc(100%-2.5rem)]" fill="none" preserveAspectRatio="none" viewBox="0 0 800 200">
                    <path d="M0,180 L130,150 L260,160 L390,70 L520,100 L650,40 L800,60" stroke="#1a42e0" stroke-width="3" vector-effect="non-scaling-stroke"></path>
                    <path d="M0,180 L130,150 L260,160 L390,70 L520,100 L650,40 L800,60 V200 H0 Z" fill="url(#gradient)" opacity="0.1"></path>
                    <defs>
                        <linearGradient id="gradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#1a42e0" />
                            <stop offset="100%" stop-color="#1a42e0" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>

        <!-- Top Courses Table -->
        <div class="border border-rule bg-surface overflow-hidden">
            <div class="px-8 py-5 border-b border-rule flex items-center justify-between">
                <h2 class="font-poppins font-bold text-lg tracking-tight text-ink">Top Performing Courses</h2>
                <button class="text-[10px] font-bold uppercase tracking-widest text-primary border-b-2 border-primary pb-0.5 hover:opacity-80 transition-opacity">Full Data</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-background-light text-[10px] font-poppins font-bold uppercase tracking-widest text-ink3">
                        <tr>
                            <th class="px-8 py-4">Course</th>
                            <th class="px-8 py-4">Instructor</th>
                            <th class="px-8 py-4 text-right">Students</th>
                            <th class="px-8 py-4 text-right">Earnings</th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px] font-body">
                        @foreach(\App\Models\Course::withCount('enrollments')->withSum(['orders' => fn($q) => $q->where('status', 'paid')], 'amount')->orderBy('enrollments_count', 'desc')->take(5)->get() as $course)
                        <tr class="border-b border-rule hover:bg-background-light/30 transition-colors">
                            <td class="px-8 py-4 font-bold text-ink">{{ $course->title }}</td>
                            <td class="px-8 py-4 text-ink2">{{ $course->instructor->name ?? 'System' }}</td>
                            <td class="px-8 py-4 text-right text-ink2">{{ number_format($course->enrollments_count) }}</td>
                            <td class="px-8 py-4 text-right font-poppins font-bold text-primary">${{ number_format($course->orders_sum_amount ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Sidebar -->
    <div class="col-span-12 lg:col-span-3 space-y-8">
        <div class="border border-rule bg-surface p-8 h-full min-h-[600px]">
            <div class="mb-10">
                <h2 class="font-poppins font-bold text-lg tracking-tight text-ink">Recent Activity</h2>
                <p class="text-[10px] text-ink3 font-bold uppercase tracking-widest mt-1.5 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    Live Updates
                </p>
            </div>
            <div class="space-y-10">
                @foreach(\App\Models\Order::with('user', 'course')->latest()->take(8)->get() as $order)
                <div class="relative pl-6 border-l border-rule">
                    <div class="absolute -left-[4.5px] top-1 w-2 h-2 rounded-full border border-surface {{ $loop->first ? 'bg-primary' : 'bg-rule' }}"></div>
                    <p class="text-[13px] font-bold text-ink leading-tight">{{ $order->user->name ?? 'Guest' }}</p>
                    <p class="text-[11px] text-ink2 mt-1">{{ optional($order->course)->title }}</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-[11px] font-poppins font-extrabold text-primary">${{ number_format($order->amount, 2) }}</span>
                        <span class="text-[10px] text-ink3 font-medium">{{ $order->created_at->diffForHumans(null, true) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
