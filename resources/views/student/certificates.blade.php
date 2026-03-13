@extends('layouts.dashboard')

@section('title', 'My Certificates')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="font-display font-extrabold text-2xl text-ink">Certificates</h1>
            <p class="text-[13px] font-body text-ink2 mt-1">Showcase your verified achievements.</p>
        </div>
        <button class="px-5 py-2.5 bg-accent text-white font-display font-bold text-[12px] rounded-card hover:opacity-90 transition-opacity">Share Profile</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach(['Advanced Web Architecture', 'Data Science Masterclass', 'UI Design Systems'] as $c)
        <div class="bg-surface border border-rule rounded-card overflow-hidden group hover:border-ink transition-colors">
            {{-- Certificate Preview --}}
            <div class="aspect-[1.414/1] bg-bg flex flex-col items-center justify-center p-12 relative">
                <div class="border-2 border-rule w-full h-full p-4 flex flex-col items-center justify-center relative bg-white">
                    <span class="font-display font-extrabold text-[10px] uppercase tracking-[0.3em] text-ink3 mb-2">LearnFlow</span>
                    <h3 class="font-display font-bold text-lg text-ink text-center mb-6 leading-tight">{{ $c }}</h3>
                    <div class="w-12 h-[1px] bg-accent mb-6"></div>
                    <span class="text-[11px] font-body text-ink2">Issued: {{ date('M d, Y') }}</span>
                </div>
                {{-- Hover utility --}}
                <div class="absolute inset-0 bg-ink/5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                    <span class="px-4 py-2 bg-white border border-rule rounded-card font-display font-bold text-[11px] uppercase tracking-wider text-ink">View Details</span>
                </div>
            </div>
            
            <div class="p-5 flex items-center justify-between border-t border-rule bg-surface">
                <span class="text-[13px] font-display font-bold text-ink">{{ $c }}</span>
                <div class="flex items-center gap-3">
                    <button class="text-ink3 hover:text-ink"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></button>
                    <button class="text-ink3 hover:text-ink"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg></button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
