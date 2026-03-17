<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Login') - {{ config('app.name', 'LearnFlow') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col items-center justify-center p-4 font-display antialiased">
        <div class="mb-8 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-3xl">school</span>
            <a href="{{ url('/') }}" wire:navigate class="text-brand-black dark:text-slate-100 text-2xl font-extrabold tracking-tight">
                Learn<span class="text-primary">Flow</span>
            </a>
        </div>

        <div class="w-full max-w-[400px]">
            {{ $slot }}
        </div>

        <footer class="mt-8 text-neutral-text dark:text-slate-500 text-[11px] uppercase tracking-widest text-center">
            © {{ date('Y') }} LearnFlow Inc.
            <a class="hover:text-brand-black dark:hover:text-slate-300 transition-colors" href="{{ route('pages.privacy') }}">Privacy</a>
            <span class="mx-1">•</span>
            <a class="hover:text-brand-black dark:hover:text-slate-300 transition-colors" href="{{ route('pages.terms') }}">Terms</a>
        </footer>
        @livewireScripts
    </body>
</html>
