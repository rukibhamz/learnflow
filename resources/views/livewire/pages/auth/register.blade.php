<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {} ?>

<div class="bg-white dark:bg-slate-900 border border-neutral-border dark:border-slate-800 shadow-sm p-8 rounded-custom">
    <h2 class="text-brand-black dark:text-slate-100 text-2xl font-bold mb-8">Create your account</h2>

    <form method="POST" action="{{ route('register.post') }}" class="space-y-6">
        @csrf
        <div class="space-y-1.5">
            <label class="block text-xs font-medium text-neutral-text dark:text-slate-400 uppercase tracking-wider" for="name">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Jane Doe" required autofocus autocomplete="name"
                class="w-full h-[36px] px-3 py-2 bg-transparent border border-neutral-border dark:border-slate-700 rounded-custom text-sm text-brand-black dark:text-slate-200 placeholder:text-neutral-text/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-sm text-red-600" />
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-medium text-neutral-text dark:text-slate-400 uppercase tracking-wider" for="username">Username</label>
            <input id="username" type="text" name="username" value="{{ old('username') }}" placeholder="johndoe" required autocomplete="username"
                pattern="[a-zA-Z0-9_]+" minlength="3" maxlength="30"
                class="w-full h-[36px] px-3 py-2 bg-transparent border border-neutral-border dark:border-slate-700 rounded-custom text-sm text-brand-black dark:text-slate-200 placeholder:text-neutral-text/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
            <x-input-error :messages="$errors->get('username')" class="mt-1 text-sm text-red-600" />
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-medium text-neutral-text dark:text-slate-400 uppercase tracking-wider" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="jane@example.com" required autocomplete="username"
                class="w-full h-[36px] px-3 py-2 bg-transparent border border-neutral-border dark:border-slate-700 rounded-custom text-sm text-brand-black dark:text-slate-200 placeholder:text-neutral-text/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-medium text-neutral-text dark:text-slate-400 uppercase tracking-wider" for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="••••••••" required autocomplete="new-password"
                class="w-full h-[36px] px-3 py-2 bg-transparent border border-neutral-border dark:border-slate-700 rounded-custom text-sm text-brand-black dark:text-slate-200 placeholder:text-neutral-text/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
        </div>

        <div class="space-y-1.5">
            <label class="block text-xs font-medium text-neutral-text dark:text-slate-400 uppercase tracking-wider" for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" required autocomplete="new-password"
                class="w-full h-[36px] px-3 py-2 bg-transparent border border-neutral-border dark:border-slate-700 rounded-custom text-sm text-brand-black dark:text-slate-200 placeholder:text-neutral-text/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-sm text-red-600" />
        </div>

        <button type="submit" class="w-full h-[44px] bg-brand-black dark:bg-primary text-white font-bold rounded-custom hover:opacity-90 transition-opacity">
            Create account
        </button>
    </form>

    <div class="relative my-8">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-neutral-border dark:border-slate-800"></div>
        </div>
        <div class="relative flex justify-center text-xs uppercase">
            <span class="bg-white dark:bg-slate-900 px-3 text-neutral-text dark:text-slate-500 font-medium tracking-widest">Or continue with</span>
        </div>
    </div>

    <a href="{{ route('auth.google') }}" class="flex w-full h-[44px] items-center justify-center gap-3 bg-white dark:bg-slate-800 border border-neutral-border dark:border-slate-700 text-brand-black dark:text-slate-200 font-medium rounded-custom hover:bg-background-light dark:hover:bg-slate-700 transition-colors">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        <span>Google</span>
    </a>

    <p class="mt-8 text-center text-xs text-neutral-text dark:text-slate-500">
        Already have an account? <a class="text-primary font-bold hover:underline" href="{{ route('login') }}" wire:navigate>Log in</a>
    </p>
</div>
