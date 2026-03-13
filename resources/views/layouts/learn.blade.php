<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lesson') – {{ config('app.name', 'LearnFlow') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-ink font-body text-white antialiased">
    <x-flash />

    {{-- Topbar --}}
    <header class="h-[52px] flex items-center justify-between px-6 bg-ink border-b border-[#222222] sticky top-0 z-40">
        <a href="{{ url('/') }}" class="flex items-center gap-2 font-display font-extrabold text-white text-lg uppercase tracking-tight">
            <x-icon name="school" class="w-6 h-6 text-accent shrink-0" />
            Learn<span class="text-accent">Flow</span>
        </a>
        
        <div class="flex-1 flex flex-col items-center max-w-2xl mx-auto px-8">
            <span class="text-[11px] font-bold uppercase tracking-widest text-[#888888] mb-1.5">{{ $courseTitle ?? 'Advanced Web Architecture' }}</span>
            <div class="w-full h-[3px] bg-[#222222] rounded-full overflow-hidden">
                <div class="h-full bg-white transition-all duration-300" style="width: {{ $progress ?? 38 }}%"></div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <span class="text-[12px] font-bold tracking-tight text-[#888888]">{{ $currentLessonOrder ?? 16 }} / {{ $totalLessonsCount ?? 42 }} lessons</span>
            <a href="{{ url('/dashboard') }}" class="text-[#888888] hover:text-white transition-colors" aria-label="Close">
                <x-icon name="close" class="w-5 h-5" />
            </a>
        </div>
    </header>

    <div class="flex min-h-[calc(100vh-52px)]">
        {{-- Main content area --}}
        <main class="flex-1 overflow-y-auto">
            @yield('content')
        </main>

        {{-- Right sidebar fixed --}}
        <aside class="w-[260px] border-l border-rule bg-surface text-ink shrink-0 overflow-y-auto hidden lg:block">
            @yield('sidebar')
        </aside>
    </div>

    @livewireScripts
</body>
</html>
