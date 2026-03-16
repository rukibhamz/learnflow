<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – {{ config('app.name', 'LearnFlow') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-bg font-sans text-ink antialiased">
    <x-flash />

    {{-- Topbar --}}
    <header class="h-[52px] flex items-center justify-between px-6 bg-surface border-b border-rule sticky top-0 z-40">
        <a href="{{ url('/') }}" class="flex items-center gap-2 font-display font-extrabold text-ink text-lg uppercase tracking-tight">
            <x-icon name="school" class="w-6 h-6 text-accent shrink-0" />
            Learn<span class="text-accent">Flow</span>
        </a>
        <div class="flex items-center gap-4">
            @livewire('notification-bell')
            <div class="w-9 h-9 rounded-full bg-accent-bg border border-accent/10 flex items-center justify-center font-display font-bold text-accent text-sm" title="{{ auth()->user()->name ?? 'User' }}">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
        </div>
    </header>

    <div class="flex">
        {{-- Sidebar --}}
        <aside class="w-[180px] min-h-[calc(100vh-52px)] fixed left-0 top-[52px] bg-surface border-r border-rule py-6 overflow-y-auto">
            <nav class="space-y-1 px-0">
                @if(isset($sidebar))
                    {{ $sidebar }}
                @else
                    @php
                        $navItems = [
                            ['label' => 'Dashboard', 'url' => url('/dashboard'), 'match' => 'dashboard'],
                            ['label' => 'My Learning', 'url' => url('/student/courses'), 'match' => 'student/courses*'],
                            ['label' => 'Certificates', 'url' => url('/student/certificates'), 'match' => 'student/certificates*'],
                            ['label' => 'Settings', 'url' => url('/student/settings'), 'match' => 'student/settings*'],
                        ];
                    @endphp

                    @foreach($navItems as $item)
                        <a href="{{ $item['url'] }}" 
                           class="flex items-center px-4 py-2.5 text-[13px] font-medium transition-all duration-150 {{ request()->is($item['match']) ? 'bg-accent-bg text-accent border-r-2 border-accent' : 'text-ink2 hover:bg-bg hover:text-ink' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                @endif
            </nav>
        </aside>

        {{-- Main --}}
        <main class="flex-1 ml-[180px] p-8">
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>
