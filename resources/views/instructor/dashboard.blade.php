@extends('layouts.dashboard')

@section('title', 'Instructor Dashboard')

@prepend('sidebar')
    @php
        $instructorNav = [
            ['label' => 'Overview', 'url' => url('/instructor/dashboard'), 'match' => 'instructor/dashboard'],
            ['label' => 'Courses', 'url' => url('/instructor/courses'), 'match' => 'instructor/courses*'],
            ['label' => 'Payments', 'url' => '#', 'match' => 'instructor/payments*'],
            ['label' => 'Settings', 'url' => '#', 'match' => 'instructor/settings*'],
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
<div class="max-w-5xl mx-auto">
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="font-display font-extrabold text-2xl text-ink">Instructor Overview</h1>
            <p class="text-[13px] font-body text-ink2 mt-1">Track your course performance and earnings.</p>
        </div>
        <a href="{{ url('/instructor/courses/create') }}" class="px-5 py-2.5 bg-ink text-white font-display font-bold text-[12px] rounded-card hover:opacity-90 transition-opacity">Create Course</a>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Total Revenue</span>
            <span class="font-display font-extrabold text-2xl text-ink">$2,480.00</span>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Enrollments</span>
            <span class="font-display font-extrabold text-2xl text-ink">1,240</span>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Course Rating</span>
            <span class="font-display font-extrabold text-2xl text-ink">4.9</span>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Active Courses</span>
            <span class="font-display font-extrabold text-2xl text-ink">6</span>
        </div>
    </div>

    {{-- Chart Placeholder --}}
    <div class="bg-surface border border-rule rounded-card p-8 mb-12">
        <div class="flex items-center justify-between mb-8">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Revenue Growth</h3>
            <select class="text-[11px] font-bold text-ink2 bg-transparent border-none focus:ring-0">
                <option>Last 30 days</option>
                <option>Last 6 months</option>
            </select>
        </div>
        <div class="h-[240px] w-full border-b border-l border-rule flex items-end justify-between px-4 pb-0.5">
            @foreach([40, 60, 45, 90, 65, 80, 75, 40, 55, 100, 85, 95] as $h)
                <div class="w-8 bg-accent-bg border-t-2 border-accent transition-all hover:bg-accent/10 cursor-pointer" style="height: {{ $h }}%"></div>
            @endforeach
        </div>
        <div class="flex justify-between mt-4 text-[10px] font-bold text-ink3 uppercase px-4">
            <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span><span>Jul</span><span>Aug</span><span>Sep</span><span>Oct</span><span>Nov</span><span>Dec</span>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-surface border border-rule rounded-card overflow-hidden">
        <div class="p-6 border-b border-rule">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Recent Enrollments</h3>
        </div>
        <div class="divide-y divide-rule">
            @foreach(range(1, 5) as $i)
            <div class="p-4 flex items-center justify-between hover:bg-bg transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 rounded-full bg-bg border border-rule flex items-center justify-center font-display font-bold text-ink2 text-[12px]">UD</div>
                    <div>
                        <p class="text-[13px] font-bold text-ink leading-none">User {{ $i }}rd</p>
                        <p class="text-[11px] text-ink3 mt-1">Enrolled in Advanced Web Architecture</p>
                    </div>
                </div>
                <span class="text-[11px] font-medium text-ink2">2 hours ago</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
