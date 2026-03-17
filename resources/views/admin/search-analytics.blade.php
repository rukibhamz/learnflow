@extends('layouts.admin')

@section('title', 'Search Analytics')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2 font-poppins">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors uppercase">Admin</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-ink uppercase">Search Analytics</span>
            </nav>
            <h1 class="font-poppins font-bold text-2xl tracking-tight text-ink">Search Analytics</h1>
            <p class="text-[13px] font-body text-ink2">Monitor search trends and discover what students are looking for.</p>
        </div>
    </div>
    @livewire('admin-search-analytics')
</div>
@endsection
