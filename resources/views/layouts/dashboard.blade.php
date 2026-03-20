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
    @stack('head')
    @stack('styles')
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
            <livewire:notification-bell lazy />
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="w-9 h-9 rounded-full bg-accent-bg border border-accent/10 flex items-center justify-center font-display font-bold text-accent text-sm hover:border-accent transition-colors"
                        title="{{ auth()->user()->name ?? 'User' }}">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </button>

                <div x-show="open"
                     x-cloak
                     @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-surface border border-rule rounded-card shadow-lg z-50 py-1">
                    <div class="px-4 py-2 border-b border-rule">
                        <p class="text-[13px] font-bold text-ink truncate">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-[11px] text-ink3 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-[13px] text-ink2 hover:bg-bg hover:text-ink transition-colors">Profile</a>
                    @if(auth()->user()?->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-[13px] text-ink2 hover:bg-bg hover:text-ink transition-colors">Admin Panel</a>
                    @endif
                    @if(auth()->user()?->hasRole('instructor'))
                    <a href="{{ route('instructor.dashboard') }}" class="block px-4 py-2 text-[13px] text-ink2 hover:bg-bg hover:text-ink transition-colors">Instructor Panel</a>
                    @endif
                    <div class="border-t border-rule mt-1 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-[13px] text-red-500 hover:bg-bg transition-colors">
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        {{-- Sidebar --}}
        <aside class="w-[180px] min-h-[calc(100vh-52px)] fixed left-0 top-[52px] bg-surface border-r border-rule py-6 overflow-y-auto">
            <nav class="space-y-1 px-0">
                @if(request()->is('instructor*'))
                    @php
                        $instructorNav = [
                            ['label' => 'Overview', 'url' => route('instructor.dashboard'), 'match' => 'instructor/dashboard'],
                            ['label' => 'Courses', 'url' => route('instructor.courses.index'), 'match' => 'instructor/courses*'],
                            ['label' => 'Earnings', 'url' => route('instructor.earnings'), 'match' => 'instructor/earnings*'],
                        ];
                    @endphp
                    @foreach($instructorNav as $item)
                        <a href="{{ $item['url'] }}"
                           class="flex items-center px-4 py-2.5 text-[13px] font-medium transition-all duration-150 {{ request()->is($item['match']) ? 'bg-accent-bg text-accent border-r-2 border-accent' : 'text-ink2 hover:bg-bg hover:text-ink' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                @elseif(isset($sidebar))
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

    @stack('scripts')
    @livewireScripts
</body>
</html>
