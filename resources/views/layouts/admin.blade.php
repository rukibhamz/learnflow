<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') – {{ config('app.name', 'LearnFlow') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-25..0" />

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-background-light text-ink antialiased font-body">

<div x-data="{ sidebarOpen: false }" class="flex min-h-screen bg-background-light">

    <!-- Mobile sidebar backdrop -->
    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition.opacity
        @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/40 z-40 lg:hidden"
    ></div>

    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed lg:sticky top-0 left-0 w-[220px] h-screen bg-surface border-r border-rule flex flex-col z-50 transition-transform duration-200 ease-in-out overflow-hidden shrink-0"
    >
        <!-- Logo -->
        <div class="h-[52px] flex items-center px-6 border-b border-rule shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">school</span>
                <span class="font-poppins font-bold text-lg tracking-tight">LearnFlow</span>
            </a>
        </div>

        <!-- Nav -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <p class="px-2 pt-2 pb-1 text-[11px] font-poppins font-bold uppercase tracking-wider text-ink3">Main</p>

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg group {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.dashboard') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">dashboard</span>
                <span class="text-sm font-medium">Overview</span>
            </a>

            <a href="{{ route('admin.courses.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg group {{ request()->routeIs('admin.courses.*') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.courses.*') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">book_5</span>
                <span class="text-sm font-medium">Courses</span>
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg group {{ request()->routeIs('admin.users.*') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.users.*') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">group</span>
                <span class="text-sm font-medium">Users</span>
            </a>

            <a href="{{ route('admin.coupons.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg group {{ request()->routeIs('admin.coupons.*') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.coupons.*') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">sell</span>
                <span class="text-sm font-medium">Coupons</span>
            </a>

            <a href="{{ route('admin.orders.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg group {{ request()->routeIs('admin.orders.*') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.orders.*') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">payments</span>
                <span class="text-sm font-medium">Orders</span>
            </a>

            <a href="{{ route('admin.search-analytics') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg group {{ request()->routeIs('admin.search-analytics') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.search-analytics') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">query_stats</span>
                <span class="text-sm font-medium">Search Analytics</span>
            </a>

            <a href="{{ route('admin.payouts') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg group {{ request()->routeIs('admin.payouts') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.payouts') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">account_balance</span>
                <span class="text-sm font-medium">Payouts</span>
            </a>

            <a href="{{ route('admin.certificate-templates') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg group {{ request()->routeIs('admin.certificate-templates') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.certificate-templates') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">workspace_premium</span>
                <span class="text-sm font-medium">Certificates</span>
            </a>

            <p class="px-2 pt-6 pb-1 text-[11px] font-poppins font-bold uppercase tracking-wider text-ink3">CMS</p>

            <a href="{{ route('admin.blogs.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg group {{ request()->routeIs('admin.blogs.*') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.blogs.*') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">article</span>
                <span class="text-sm font-medium">Blog Posts</span>
            </a>

            <a href="{{ route('admin.hero.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg group {{ request()->routeIs('admin.hero.*') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.hero.*') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">view_carousel</span>
                <span class="text-sm font-medium">Hero Slider</span>
            </a>

            <p class="px-2 pt-6 pb-1 text-[11px] font-poppins font-bold uppercase tracking-wider text-ink3">Account</p>

            <a href="{{ route('admin.settings') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-lg group {{ request()->routeIs('admin.settings') ? 'bg-primary/10 text-primary' : 'text-ink hover:bg-background-light' }}">
                <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.settings') ? 'text-primary' : 'text-ink3 group-hover:text-ink' }}">settings</span>
                <span class="text-sm font-medium">Settings</span>
            </a>
        </nav>

        <!-- User footer -->
        <div class="p-4 border-t border-rule bg-surface shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center shrink-0">
                    <span class="text-primary font-bold text-xs">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</span>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                    <p class="text-[10px] text-ink3 truncate">{{ auth()->user()->email ?? 'admin@learnflow.io' }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main content wrapper -->
    <div class="flex flex-col min-w-0 flex-1 overflow-hidden">

        <!-- Topbar -->
        <header class="h-[52px] min-h-[52px] w-full bg-surface border-b border-rule flex items-center justify-between px-6 lg:px-10 sticky top-0 z-40">
            <div class="flex items-center gap-3 flex-1">
                <!-- Mobile hamburger -->
                <button @click="sidebarOpen = true" class="lg:hidden w-9 h-9 flex items-center justify-center hover:bg-background-light rounded-lg">
                    <span class="material-symbols-outlined text-[20px] text-ink3">menu</span>
                </button>
                <form action="{{ route('admin.courses.index') }}" method="GET" class="relative w-full max-w-md">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink3">search</span>
                    <input type="text" name="q" placeholder="Search courses…" value="{{ request('q') }}"
                           class="w-full bg-background-light border-none rounded-lg py-1.5 pl-10 pr-4 text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                </form>
            </div>
            <div class="flex items-center gap-2">
                <livewire:notification-bell lazy />
                <a href="https://laravel.com/docs" target="_blank" rel="noopener" class="w-9 h-9 flex items-center justify-center hover:bg-background-light rounded-lg" title="Help & Documentation">
                    <span class="material-symbols-outlined text-[20px] text-ink3">help</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-9 h-9 flex items-center justify-center hover:bg-background-light rounded-lg text-ink3 hover:text-red-500 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page content -->
        <main class="p-6 lg:p-10 w-full max-w-full">
            @yield('content')
        </main>
    </div>
</div>

@livewireScripts
@stack('scripts')
</body>
</html>
