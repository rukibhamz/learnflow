<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('head')

    <title>@yield('title', config('app.name', 'LearnFlow'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @if(isset($siteFaviconUrl))
        <link rel="icon" type="image/x-icon" href="{{ $siteFaviconUrl }}">
    @endif

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
    @include('partials.brand-styles')
</head>
<body class="min-h-screen bg-bg font-sans text-ink antialiased">
    <div class="min-h-screen flex flex-col">
        <livewire:layout.navigation />

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-surface border-b border-rule shadow-sm">
                <div class="max-w-7xl mx-auto py-6 px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        {{-- Maintenance mode banner for admins/instructors --}}
        @if(config('settings.maintenance_mode') && auth()->check() && auth()->user()?->hasRole(['admin', 'instructor']))
        <div x-data="{ show: true }" x-show="show" x-cloak
             class="bg-amber-50 border-b border-amber-200 px-4 py-2.5 flex items-center justify-between gap-4 text-sm">
            <div class="flex items-center gap-2 text-amber-800">
                <span class="material-symbols-outlined text-[18px] text-amber-600">construction</span>
                <span class="font-semibold">Maintenance mode is active.</span>
                <span class="text-amber-700">Visitors see a "Coming Soon" page. You can see this content because you're an admin.</span>
            </div>
            <button @click="show = false" class="text-amber-600 hover:text-amber-800 transition-colors shrink-0">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        @endif

        <!-- Page Content -->
        <main class="flex-grow flex flex-col">
            @if(isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>

        <x-footer />
    </div>
    @livewireScripts
    <style>
        [x-cloak] { display: none !important; }
        
        /* Global Animations */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const reveals = document.querySelectorAll('.reveal');
            const observerOptions = { threshold: 0.15, rootMargin: '0px 0px -50px 0px' };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        // Optional: unobserve after reveal
                        // observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            reveals.forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>
