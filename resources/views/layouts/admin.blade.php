<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') – {{ config('app.name', 'LearnFlow') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-25..0" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-background-light text-ink antialiased font-body">
    <div class="flex min-h-screen bg-background-light overflow-x-hidden">
        <!-- Sidebar -->
        <aside class="w-[220px] shrink-0 h-screen sticky top-0 bg-surface border-r border-rule lg:flex hidden flex-col z-50 overflow-hidden">
            <div class="h-[52px] flex items-center px-6 border-b border-rule shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">school</span>
                    <span class="font-syne font-bold text-lg tracking-tight">LearnFlow</span>
                </a>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <div class="px-2 py-2 text-[11px] font-syne font-bold uppercase tracking-wider text-ink3 mb-2">Main</div>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }} group">
                    <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.dashboard') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">dashboard</span>
                    <span class="text-sm font-medium">Overview</span>
                </a>
                <a href="{{ route('admin.courses.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.courses.*') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }} group">
                    <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.courses.*') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">book_5</span>
                    <span class="text-sm font-medium">Courses</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }} group">
                    <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.users.*') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">group</span>
                    <span class="text-sm font-medium">Users</span>
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-ink hover:bg-background-light group">
                    <span class="material-symbols-outlined text-[20px] text-ink3 group-hover:text-ink">payments</span>
                    <span class="text-sm font-medium">Finance</span>
                </a>
                
                <div class="pt-6 px-2 py-2 text-[11px] font-syne font-bold uppercase tracking-wider text-ink3 mb-2">Account</div>
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.settings') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }} group">
                    <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.settings') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">settings</span>
                    <span class="text-sm font-medium">Settings</span>
                </a>
            </nav>
            <div class="p-4 border-t border-rule bg-surface shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                        <span class="text-primary font-bold text-xs">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</span>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                        <p class="text-[10px] text-ink3 truncate">{{ auth()->user()->email ?? 'admin@learnflow.io' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex flex-col min-w-0 w-full overflow-hidden">
            <!-- Topbar (matches Main padding) -->
            <header class="h-[52px] min-h-[52px] w-full bg-surface border-b border-rule flex items-center justify-between lg:px-10 px-6 sticky top-0 z-40">
                <div class="flex items-center gap-4 flex-1">
                    <div class="relative w-full max-w-md">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink3">search</span>
                        <input type="text" placeholder="Search resources..." class="w-full bg-background-light border-none rounded-lg py-1.5 pl-10 pr-4 text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="w-9 h-9 flex items-center justify-center hover:bg-background-light rounded-lg relative">
                        <span class="material-symbols-outlined text-[20px] text-ink3">notifications</span>
                        @livewire('notification-bell')
                    </button>
                    <button class="w-9 h-9 flex items-center justify-center hover:bg-background-light rounded-lg">
                        <span class="material-symbols-outlined text-[20px] text-ink3">help</span>
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-9 h-9 flex items-center justify-center hover:bg-background-light rounded-lg text-ink3 hover:text-red-500 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <main class="lg:p-10 p-6 w-full max-w-full">
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
