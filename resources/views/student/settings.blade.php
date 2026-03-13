@extends('layouts.dashboard')

@section('title', 'Settings')

@section('content')
<div class="max-w-3xl">
    <div class="mb-10">
        <h1 class="font-display font-extrabold text-2xl text-ink">Account Settings</h1>
        <p class="text-[13px] font-body text-ink2 mt-1">Manage your profile and account preferences.</p>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-8 border-b border-rule mb-10" x-data="{ tab: 'profile' }">
        <button @click="tab = 'profile'" :class="tab === 'profile' ? 'border-accent text-ink' : 'border-transparent text-ink3 hover:text-ink2'" class="pb-4 font-display font-bold text-[13px] border-b-2 transition-colors">Profile</button>
        <button @click="tab = 'password'" :class="tab === 'password' ? 'border-accent text-ink' : 'border-transparent text-ink3 hover:text-ink2'" class="pb-4 font-display font-bold text-[13px] border-b-2 transition-colors">Password</button>
        <button @click="tab = 'notifications'" :class="tab === 'notifications' ? 'border-accent text-ink' : 'border-transparent text-ink3 hover:text-ink2'" class="pb-4 font-display font-bold text-[13px] border-b-2 transition-colors">Notifications</button>
        <button @click="tab = 'billing'" :class="tab === 'billing' ? 'border-accent text-ink' : 'border-transparent text-ink3 hover:text-ink2'" class="pb-4 font-display font-bold text-[13px] border-b-2 transition-colors">Billing</button>
    </div>

    {{-- Profile Section --}}
    <form class="space-y-8">
        <div>
            <h3 class="font-display font-bold text-sm text-ink mb-6">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Full Name</label>
                    <input type="text" value="{{ auth()->user()->name ?? 'Learner One' }}" class="w-full h-9 bg-surface border border-rule rounded-card px-3 font-body text-sm focus:outline-none focus:border-accent">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Email Address</label>
                    <input type="email" value="{{ auth()->user()->email ?? 'learner@example.com' }}" class="w-full h-9 bg-surface border border-rule rounded-card px-3 font-body text-sm focus:outline-none focus:border-accent text-ink3 cursor-not-allowed" readonly>
                </div>
            </div>
        </div>

        <div>
            <h3 class="font-display font-bold text-sm text-ink mb-4">Profile Picture</h3>
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 rounded-full bg-accent-bg border border-accent/10 flex items-center justify-center font-display font-bold text-accent text-2xl">
                    {{ strtoupper(substr(auth()->user()->name ?? 'L', 0, 1)) }}
                </div>
                <div class="space-y-2">
                    <button type="button" class="px-5 py-2 border border-rule rounded-card text-[12px] font-display font-bold text-ink hover:border-ink transition-colors">Change photo</button>
                    <p class="text-[11px] text-ink3 font-body">JPG, PNG or WEBP. Max 2MB.</p>
                </div>
            </div>
        </div>

        <div class="pt-8 border-t border-rule">
            <button type="submit" class="px-8 py-3 bg-ink text-white font-display font-bold text-[13px] rounded-card hover:opacity-90 transition-opacity">Save changes</button>
        </div>
    </form>
</div>
@endsection
