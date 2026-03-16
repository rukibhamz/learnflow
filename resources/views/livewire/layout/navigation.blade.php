<?php

use Livewire\Volt\Component;

new class extends Component {} ?>

<nav x-data="{ open: false }" class="bg-surface border-b border-rule h-20 flex items-center">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-6 w-full">
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
                    <a href="{{ route('courses.index') }}#mentors" class="text-sm font-medium text-ink2 hover:text-accent transition-colors">Mentors</a>
                    <a href="{{ route('courses.index') }}#pricing" class="text-sm font-medium text-ink2 hover:text-accent transition-colors">Pricing</a>
                    <a href="{{ route('courses.index') }}" class="text-sm font-medium text-ink2 hover:text-accent transition-colors">Resources</a>
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
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name ?? '']) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email ?? '' }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile')" wire:navigate>
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-700 transition">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="px-4 space-y-2">
                    <a href="{{ route('login') }}" class="block text-base font-medium text-ink2">Log In</a>
                    <a href="{{ route('register') }}" class="block text-base font-medium text-accent">Sign Up</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
