<?php

use Livewire\Volt\Component;

new class extends Component {} ?>

<nav x-data="{ open: false }" class="bg-surface border-b border-rule h-20 flex items-center sticky top-0 z-50 w-full">
    <!-- Primary Navigation Menu -->
    <div class="px-6 md:px-10 w-full">
        <div class="flex justify-between h-20 items-center">
            <div class="flex items-center gap-12">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <x-icon name="school" class="w-8 h-8 text-accent shrink-0" />
                        <span class="text-xl font-bold tracking-tight font-display text-ink">LearnFlow</span>
                    </a>
                </div>
 
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:flex">
                    <a href="{{ route('courses.index') }}" class="text-sm font-medium {{ request()->routeIs('courses*') ? 'text-accent' : 'text-ink2 hover:text-accent' }} transition-colors">Courses</a>
                    <a href="{{ route('pages.mentors') }}" class="text-sm font-medium {{ request()->routeIs('pages.mentors') ? 'text-accent' : 'text-ink2 hover:text-accent' }} transition-colors">Mentors</a>
                    <a href="{{ route('pages.pricing') }}" class="text-sm font-medium {{ request()->routeIs('pages.pricing') ? 'text-accent' : 'text-ink2 hover:text-accent' }} transition-colors">Pricing</a>
                    <a href="{{ route('pages.resources') }}" class="text-sm font-medium {{ request()->routeIs('pages.resources') ? 'text-accent' : 'text-ink2 hover:text-accent' }} transition-colors">Resources</a>
                    @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-accent' : 'text-ink2 hover:text-accent' }} transition-colors">Dashboard</a>
                    @endauth
                </div>
            </div>
 
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-4 py-2 border border-rule bg-bg text-sm font-bold text-ink hover:border-accent transition-colors">
                            <div x-data="{{ json_encode(['name' => auth()->user()->name ?? 'Guest']) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
 
                            <x-icon name="chevron-down" class="w-4 h-4 text-ink3" />
                        </button>
                    </x-slot>
 
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile')" wire:navigate>
                            {{ __('Profile') }}
                        </x-dropdown-link>
 
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-slate-700 transition duration-150 ease-in-out">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-ink2 hover:text-accent transition-colors" wire:navigate>Log In</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-accent text-white text-sm font-bold rounded-custom hover:opacity-90 transition-opacity" wire:navigate>Sign Up</a>
                </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-ink2 hover:text-ink hover:bg-bg focus:outline-none focus:bg-bg focus:text-ink transition duration-150 ease-in-out">
                    <span class="material-symbols-outlined text-[28px]">menu</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div x-show="open" 
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-40 bg-ink/50 backdrop-blur-sm sm:hidden" 
         @click="open = false" x-cloak></div>

    <!-- Mobile Menu Flyout -->
    <div x-show="open" 
         x-transition:enter="transition ease-in-out duration-300 transform" 
         x-transition:enter-start="translate-x-full" 
         x-transition:enter-end="translate-x-0" 
         x-transition:leave="transition ease-in-out duration-300 transform" 
         x-transition:leave-start="translate-x-0" 
         x-transition:leave-end="translate-x-full" 
         class="fixed inset-y-0 right-0 z-50 w-full max-w-sm bg-surface overflow-y-auto sm:hidden flex flex-col shadow-2xl border-l border-rule" x-cloak>
        
        <div class="flex items-center justify-between px-6 h-20 border-b border-rule shrink-0">
            <div class="flex items-center gap-2">
                <x-icon name="school" class="w-8 h-8 text-accent shrink-0" />
                <span class="text-xl font-bold tracking-tight font-display text-ink">LearnFlow</span>
            </div>
            <button @click="open = false" class="p-2 text-ink2 hover:text-ink transition-colors bg-bg rounded-lg">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <div class="px-6 py-8 space-y-6 flex-1 bg-white">
            <div class="flex flex-col gap-5 border-b border-rule pb-8">
                <a href="{{ route('courses.index') }}" class="text-lg font-bold font-display {{ request()->routeIs('courses*') ? 'text-accent' : 'text-ink2 hover:text-accent' }} transition-colors">Courses</a>
                <a href="{{ route('pages.mentors') }}" class="text-lg font-bold font-display {{ request()->routeIs('pages.mentors') ? 'text-accent' : 'text-ink2 hover:text-accent' }} transition-colors">Mentors</a>
                <a href="{{ route('pages.pricing') }}" class="text-lg font-bold font-display {{ request()->routeIs('pages.pricing') ? 'text-accent' : 'text-ink2 hover:text-accent' }} transition-colors">Pricing</a>
                <a href="{{ route('pages.resources') }}" class="text-lg font-bold font-display {{ request()->routeIs('pages.resources') ? 'text-accent' : 'text-ink2 hover:text-accent' }} transition-colors">Resources</a>
                @auth
                <a href="{{ route('dashboard') }}" class="text-lg font-bold font-display {{ request()->routeIs('dashboard') ? 'text-accent' : 'text-ink2 hover:text-accent' }} transition-colors">Dashboard</a>
                @endauth
            </div>

            <div class="pt-4">
                @auth
                    <div class="mb-6 bg-bg p-4 rounded-xl border border-rule">
                        <div class="font-bold text-base font-display text-ink" x-data="{{ json_encode(['name' => auth()->user()->name ?? '']) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                        <div class="font-medium text-xs text-ink3 mt-1">{{ auth()->user()->email ?? '' }}</div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <a href="{{ route('profile') }}" wire:navigate class="text-base font-bold font-display text-ink2 hover:text-accent transition-colors flex items-center gap-3">
                            <span class="material-symbols-outlined text-[20px]">person</span>
                            {{ __('Profile') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="text-left w-full text-base font-bold font-display text-red-600 hover:text-red-700 transition flex items-center gap-3">
                                <span class="material-symbols-outlined text-[20px]">logout</span>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        <a href="{{ route('login') }}" class="py-3.5 px-4 text-center border-2 border-rule rounded-custom text-base font-bold font-display text-ink hover:border-ink transition-colors">Log In</a>
                        <a href="{{ route('register') }}" class="py-3.5 px-4 text-center bg-accent rounded-custom text-base font-bold font-display text-white hover:opacity-90 transition-opacity">Sign Up</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
