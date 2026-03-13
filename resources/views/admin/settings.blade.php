@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="max-w-3xl">
    <div class="mb-10">
        <h1 class="font-display font-extrabold text-2xl text-ink">Platform Settings</h1>
        <p class="text-[13px] font-body text-ink2 mt-1">Global configuration for the LearnFlow LMS.</p>
    </div>

    <form class="space-y-12">
        {{-- General Section --}}
        <div>
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest mb-6">General Configuration</h3>
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Site Name</label>
                    <input type="text" value="LearnFlow" class="w-full h-11 bg-surface border border-rule rounded-card px-4 font-body text-sm focus:outline-none focus:border-accent">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Support Email</label>
                    <input type="email" value="support@learnflow.ai" class="w-full h-11 bg-surface border border-rule rounded-card px-4 font-body text-sm focus:outline-none focus:border-accent">
                </div>
            </div>
        </div>

        {{-- Payment Section --}}
        <div>
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest mb-6">Payment Gateway</h3>
            <div class="p-6 bg-surface border border-rule rounded-card">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-accent-bg flex items-center justify-center rounded-card">
                            <svg class="w-6 h-6 text-accent" fill="currentColor" viewBox="0 0 24 24"><path d="M13.976 9.15c-2.172-.112-2.547 1.218-2.547 1.874 0 1.038.868 1.463 2.584 1.873 1.02.244 3.033.724 3.033 2.768 0 2.238-2.146 3.193-4.437 3.193-2.33 0-4.506-1.015-4.506-3.118h2.645c0 1.077.94 1.463 1.862 1.463.923 0 1.794-.35 1.794-1.256 0-.671-.568-1.078-1.503-1.31-1.042-.257-4.114-.972-4.114-3.328 0-2.173 2.455-3.194 4.302-3.194 2.112 0 4.1.923 4.1 2.9h-2.106c0-.95-.733-1.638-2.107-1.864zM22 12c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2s10 4.477 10 10zm-2 0a8 8 0 10-16 0 8 8 0 0016 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[13px] font-bold text-ink leading-tight">Stripe Integration</p>
                            <p class="text-[11px] text-ink3 mt-1">Connected and operational</p>
                        </div>
                    </div>
                    <button type="button" class="text-[11px] font-bold text-ink hover:text-accent uppercase tracking-widest transition-colors">Configure</button>
                </div>
            </div>
        </div>

        {{-- Features Toggles --}}
        <div>
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest mb-6">Feature Flags</h3>
            <div class="space-y-4">
                <label class="flex items-center justify-between p-4 bg-bg border border-rule rounded-card cursor-pointer group">
                    <div class="space-y-0.5">
                        <span class="text-[13px] font-bold text-ink">Instructor Approvals</span>
                        <p class="text-[11px] text-ink3">Manual review required for new instructors</p>
                    </div>
                    <div class="w-10 h-5 bg-accent rounded-full relative">
                        <div class="absolute right-1 top-1 w-3 h-3 bg-white rounded-full"></div>
                    </div>
                </label>
                <label class="flex items-center justify-between p-4 bg-bg border border-rule rounded-card cursor-pointer group">
                    <div class="space-y-0.5">
                        <span class="text-[13px] font-bold text-ink">Course Gamification</span>
                        <p class="text-[11px] text-ink3">Enable points and leaderboard system</p>
                    </div>
                    <div class="w-10 h-5 bg-rule rounded-full relative">
                        <div class="absolute left-1 top-1 w-3 h-3 bg-white rounded-full"></div>
                    </div>
                </label>
            </div>
        </div>

        <div class="pt-8 border-t border-rule">
            <button type="submit" class="px-8 py-3 bg-ink text-white font-display font-bold text-[13px] rounded-card hover:opacity-90 transition-opacity">Save All Changes</button>
        </div>
    </form>
</div>
@endsection
