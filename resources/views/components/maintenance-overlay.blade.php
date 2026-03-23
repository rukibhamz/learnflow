@extends('layouts.app')

@section('title', 'Coming Soon — ' . ($siteName ?? config('app.name')))

@section('content')
<div class="flex-1 flex items-center justify-center min-h-[70vh] px-6 py-20">
    <div class="max-w-lg w-full text-center flex flex-col items-center gap-8">

        {{-- Icon --}}
        <div class="size-20 rounded-full bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-[40px] text-primary">construction</span>
        </div>

        {{-- Heading --}}
        <div class="flex flex-col gap-3">
            <p class="text-accent font-bold tracking-[0.2em] text-xs uppercase">
                {{ $siteName ?? config('app.name') }}
            </p>
            <h1 class="text-4xl sm:text-5xl font-extrabold font-display text-ink leading-tight">
                Coming Soon
            </h1>
            <p class="text-ink2 text-lg font-body leading-relaxed">
                {{ \App\Models\Setting::get('maintenance_coming_soon_message', "This section is currently under maintenance. We're working hard to bring you something great — check back soon.") }}
            </p>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap justify-center gap-4 pt-2">
            <a href="{{ route('home') }}"
               class="bg-primary text-white px-8 py-3 text-sm font-bold rounded-lg hover:opacity-90 hover:-translate-y-1 transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">home</span>
                Back to Home
            </a>

            @if(auth()->check() && auth()->user()->hasRole('student'))
                <a href="{{ route('dashboard') }}"
                   class="border-2 border-rule text-ink px-8 py-3 text-sm font-bold rounded-lg hover:bg-ink hover:text-white hover:-translate-y-1 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">dashboard</span>
                    Go to Dashboard
                </a>
            @endif
        </div>

    </div>
</div>
@endsection
