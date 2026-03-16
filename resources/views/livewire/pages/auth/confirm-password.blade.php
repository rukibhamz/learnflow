<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {} ?>

<div class="bg-white dark:bg-slate-900 border border-neutral-border dark:border-slate-800 p-8 rounded-custom">
    <h2 class="text-brand-black dark:text-slate-100 text-2xl font-bold mb-4">Confirm Password</h2>

    <div class="mb-6 text-sm text-neutral-text dark:text-slate-400">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm.post') }}" class="space-y-6">
        @csrf
        <div class="space-y-1.5">
            <label class="block text-xs font-medium text-neutral-text dark:text-slate-400 uppercase tracking-wider" for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="••••••••" required autocomplete="current-password"
                class="w-full h-[36px] px-3 py-2 bg-transparent border border-neutral-border dark:border-slate-700 rounded-custom text-sm text-brand-black dark:text-slate-200 placeholder:text-neutral-text/50 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-all" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
        </div>

        <button type="submit" class="w-full h-[44px] bg-brand-black dark:bg-primary text-white font-bold rounded-custom hover:opacity-90 transition-opacity">
            Confirm
        </button>
    </form>
</div>
