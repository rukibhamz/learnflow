<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') – {{ config('app.name', 'LearnFlow') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="min-h-screen bg-bg font-body text-ink antialiased">
    <x-flash />

    {{-- Topbar --}}
    <header class="h-[52px] flex items-center justify-between px-6 bg-surface border-b border-rule sticky top-0 z-40">
        <div class="flex items-center gap-4">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-display font-extrabold text-ink text-lg uppercase tracking-tight">
                <x-icon name="school" class="w-6 h-6 text-accent shrink-0" />
                Learn<span class="text-accent">Flow</span>
            </a>
            <span class="text-[11px] font-bold uppercase tracking-widest text-ink3 border-l border-rule pl-4">Admin Console</span>
        </div>
        <div class="flex items-center gap-4">
            @livewire('notification-bell')
            <div class="w-9 h-9 rounded-full bg-ink flex items-center justify-center font-display font-bold text-white text-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
            </div>
        </div>
    </header>

    <div class="flex">
        {{-- Sidebar --}}
        <aside class="w-[220px] min-h-[calc(100vh-52px)] fixed left-0 top-[52px] bg-surface border-r border-rule py-8 overflow-y-auto">
            <nav class="space-y-8 px-0">
                @php
                    $groups = [
                        'Overview' => [
                            ['label' => 'Dashboard', 'url' => url('/admin/dashboard')],
                            ['label' => 'Revenue', 'url' => '#'],
                        ],
                        'Courses' => [
                            ['label' => 'Review Queue', 'url' => url('/admin/courses/review')],
                            ['label' => 'All Courses', 'url' => '#'],
                        ],
                        'Users' => [
                            ['label' => 'User List', 'url' => url('/admin/users')],
                            ['label' => 'Instructors', 'url' => '#'],
                        ],
                        'Finance' => [
                            ['label' => 'Payouts', 'url' => '#'],
                            ['label' => 'Transactions', 'url' => '#'],
                        ],
                        'Settings' => [
                            ['label' => 'System Settings', 'url' => url('/admin/settings')],
                        ],
                    ];
                @endphp

                @foreach($groups as $groupName => $items)
                    <div>
                        <h3 class="px-6 font-display font-bold text-[10px] uppercase tracking-[0.15em] text-ink3 mb-3">{{ $groupName }}</h3>
                        <div class="space-y-0.5">
                            @foreach($items as $item)
                                <a href="{{ $item['url'] }}" 
                                   class="flex items-center px-6 py-2 text-[13px] font-medium transition-all duration-150 {{ request()->is(trim(parse_url($item['url'], PHP_URL_PATH), '/')) ? 'bg-accent-bg text-accent border-r-2 border-accent' : 'text-ink2 hover:bg-bg hover:text-ink' }}">
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>
        </aside>

        {{-- Main --}}
        <main class="flex-1 ml-[220px] p-8">
            <header class="mb-8">
                <h1 class="font-display font-extrabold text-2xl text-ink">@yield('title', 'Admin Panel')</h1>
            </header>
            @yield('content')
        </main>
    </div>

    @livewireScripts
</body>
</html>
