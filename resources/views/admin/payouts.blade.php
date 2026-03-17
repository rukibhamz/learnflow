@extends('layouts.admin')

@section('title', 'Instructor Payouts')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2 font-poppins">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors uppercase">Admin</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-ink uppercase">Payouts</span>
            </nav>
            <h1 class="font-poppins font-bold text-2xl tracking-tight text-ink">Instructor Payouts</h1>
            <p class="text-[13px] font-body text-ink2">Manage instructor earnings and payout history.</p>
        </div>
    </div>
    @livewire('admin-payouts')
</div>
@endsection
