@extends('layouts.admin')

@section('title', 'System Overview')

@section('content')
<div class="space-y-12">
    {{-- Platform KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Total Users</span>
            <div class="flex items-end gap-2">
                <span class="font-display font-extrabold text-2xl text-ink leading-none">24,102</span>
                <span class="text-[11px] font-bold text-success mb-0.5">↑ 4%</span>
            </div>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Monthly Revenue</span>
            <span class="font-display font-extrabold text-2xl text-ink">$84,200</span>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Course Approvals</span>
            <div class="flex items-end gap-2">
                <span class="font-display font-extrabold text-2xl text-accent leading-none">12</span>
                <span class="text-[11px] font-bold text-ink3 mb-0.5">pending</span>
            </div>
        </div>
        <div class="bg-surface border border-rule rounded-card p-6">
            <span class="text-[10px] font-bold uppercase tracking-widest text-ink3 mb-2 block">Active Subs</span>
            <span class="font-display font-extrabold text-2xl text-ink">1,840</span>
        </div>
    </div>

    {{-- Platform Status --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-8">
        {{-- Revenue Split / Distribution --}}
        <div class="bg-surface border border-rule rounded-card p-8">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest mb-8">Revenue Distribution</h3>
            <div class="space-y-6">
                @foreach([
                    ['label' => 'Direct Sales', 'val' => 64, 'color' => '#1A43E0'],
                    ['label' => 'Subscriptions', 'val' => 28, 'color' => '#1B7A3E'],
                    ['label' => 'Team Licenses', 'val' => 8, 'color' => '#96650A'],
                ] as $item)
                <div class="space-y-2">
                    <div class="flex justify-between text-[12px] font-bold text-ink2">
                        <span>{{ $item['label'] }}</span>
                        <span>{{ $item['val'] }}%</span>
                    </div>
                    <div class="w-full h-2 bg-bg rounded-pill overflow-hidden">
                        <div class="h-full" style="width: {{ $item['val'] }}%; background-color: {{ $item['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- System Health --}}
        <div class="bg-surface border border-rule rounded-card p-6">
            <h3 class="font-display font-bold text-[11px] uppercase tracking-widest text-ink3 mb-6">Service Status</h3>
            <ul class="space-y-4">
                <li class="flex items-center justify-between text-[13px] font-body text-ink2">
                    <span>Main API</span>
                    <span class="text-success font-bold text-[10px] uppercase">Operational</span>
                </li>
                <li class="flex items-center justify-between text-[13px] font-body text-ink2">
                    <span>Video Processing</span>
                    <span class="text-success font-bold text-[10px] uppercase">Operational</span>
                </li>
                <li class="flex items-center justify-between text-[13px] font-body text-ink2">
                    <span>Mail Server</span>
                    <span class="text-warn font-bold text-[10px] uppercase">Delayed</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
