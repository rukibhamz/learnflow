@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="max-w-3xl space-y-12">
    <div class="space-y-1">
        <h2 class="font-poppins font-bold text-lg tracking-tight text-ink">Platform Configuration</h2>
        <p class="text-[13px] font-body text-ink2">Manage global settings, integrations, and platform health.</p>
    </div>

    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-100 rounded-none text-sm text-green-800 font-body">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="p-4 bg-red-50 border border-red-100 rounded-none text-sm text-red-800 font-body">
            {{ session('error') }}
        </div>
    @endif

    {{-- Mailer Section --}}
    <section class="space-y-6" x-data="{ mailer: '{{ \App\Models\Setting::get('mail_mailer', config('mail.default')) }}' }">
        <h3 class="font-poppins font-bold text-[11px] uppercase tracking-widest text-ink3 border-l-2 border-primary pl-4">Mailer Configuration</h3>
        
        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-8 bg-surface border border-rule space-y-8">
            @csrf
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Mail Mailer</label>
                    <select name="mail_mailer" x-model="mailer" class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                        <option value="smtp">SMTP</option>
                        <option value="ses">Amazon SES</option>
                        <option value="mailgun">Mailgun</option>
                        <option value="postmark">Postmark</option>
                        <option value="log">Log (Local Debug)</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Encryption</label>
                    <select name="mail_encryption" class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                        <option value="tls" {{ \App\Models\Setting::get('mail_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ \App\Models\Setting::get('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="null" {{ \App\Models\Setting::get('mail_encryption') == 'null' ? 'selected' : '' }}>None</option>
                    </select>
                </div>

                {{-- SMTP Fields --}}
                <template x-if="mailer === 'smtp'">
                    <div class="col-span-2 grid grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">SMTP Host</label>
                            <input type="text" name="mail_host" value="{{ \App\Models\Setting::get('mail_host', config('mail.mailers.smtp.host')) }}" 
                                class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none" placeholder="smtp.mailtrap.io">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">SMTP Port</label>
                            <input type="text" name="mail_port" value="{{ \App\Models\Setting::get('mail_port', config('mail.mailers.smtp.port')) }}" 
                                class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none" placeholder="2525">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Username</label>
                            <input type="text" name="mail_username" value="{{ \App\Models\Setting::get('mail_username', config('mail.mailers.smtp.username')) }}" 
                                class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Password</label>
                            <input type="password" name="mail_password" value="{{ \App\Models\Setting::get('mail_password', config('mail.mailers.smtp.password')) }}" 
                                class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                        </div>
                    </div>
                </template>

                {{-- SES Fields --}}
                <template x-if="mailer === 'ses'">
                    <div class="col-span-2 grid grid-cols-3 gap-8">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">SES Key</label>
                            <input type="text" name="mail_ses_key" value="{{ \App\Models\Setting::get('mail_ses_key') }}" 
                                class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">SES Secret</label>
                            <input type="password" name="mail_ses_secret" value="{{ \App\Models\Setting::get('mail_ses_secret') }}" 
                                class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">SES Region</label>
                            <input type="text" name="mail_ses_region" value="{{ \App\Models\Setting::get('mail_ses_region', 'us-east-1') }}" 
                                class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                        </div>
                    </div>
                </template>

                {{-- Mailgun Fields --}}
                <template x-if="mailer === 'mailgun'">
                    <div class="col-span-2 grid grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Mailgun Domain</label>
                            <input type="text" name="mail_mailgun_domain" value="{{ \App\Models\Setting::get('mail_mailgun_domain') }}" 
                                class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Mailgun Secret</label>
                            <input type="password" name="mail_mailgun_secret" value="{{ \App\Models\Setting::get('mail_mailgun_secret') }}" 
                                class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                        </div>
                    </div>
                </template>

                {{-- Postmark Fields --}}
                <template x-if="mailer === 'postmark'">
                    <div class="col-span-2 space-y-2">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Postmark Token</label>
                        <input type="password" name="mail_postmark_token" value="{{ \App\Models\Setting::get('mail_postmark_token') }}" 
                            class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                    </div>
                </template>

                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">From Address</label>
                    <input type="email" name="mail_from_address" value="{{ \App\Models\Setting::get('mail_from_address', config('mail.from.address')) }}" 
                        class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">From Name</label>
                    <input type="text" name="mail_from_name" value="{{ \App\Models\Setting::get('mail_from_name', config('mail.from.name')) }}" 
                        class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-8 py-3 bg-primary text-white font-poppins font-bold text-xs uppercase tracking-widest hover:opacity-90 transition-opacity">
                    Update Mailer Settings
                </button>
            </div>
        </form>

        <div class="p-8 bg-surface/50 border border-rule border-t-0 space-y-6">
            <h4 class="text-[10px] font-bold uppercase tracking-widest text-ink3">Connection Test</h4>
            <form method="POST" action="{{ route('admin.settings.test-email') }}" class="flex gap-4 items-end">
                @csrf
                <div class="flex-1 space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Send test email to</label>
                    <input type="email" name="test_email" value="{{ auth()->user()?->email }}" 
                        class="w-full h-11 bg-background-light/50 border border-rule/50 rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30 outline-none">
                </div>
                <button type="submit" class="h-11 px-6 border border-ink text-ink font-poppins font-bold text-xs uppercase tracking-widest hover:bg-ink hover:text-white transition-all">
                    Run Test
                </button>
            </form>
        </div>
    </section>

    <form class="space-y-12">
        {{-- General Section --}}
        <section class="space-y-6">
            <h3 class="font-poppins font-bold text-[11px] uppercase tracking-widest text-ink3 border-l-2 border-primary pl-4">General Configuration</h3>
            <div class="grid grid-cols-1 gap-6 p-8 bg-surface border border-rule">
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Site Name</label>
                    <input type="text" value="{{ config('app.name') }}" class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2">Support Email</label>
                    <input type="email" value="support@learnflow.ai" class="w-full h-11 bg-background-light border-none rounded-lg px-4 font-body text-sm focus:ring-1 focus:ring-primary/30">
                </div>
            </div>
        </section>

        {{-- Payment Section --}}
        <section class="space-y-6">
            <h3 class="font-poppins font-bold text-[11px] uppercase tracking-widest text-ink3 border-l-2 border-primary pl-4">Payment Gateway</h3>
            <div class="p-8 bg-surface border border-rule">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-primary/5 flex items-center justify-center rounded-lg border border-primary/10">
                            <span class="material-symbols-outlined text-primary">payments</span>
                        </div>
                        <div>
                            <p class="text-[13px] font-bold text-ink font-body">Stripe Integration</p>
                            <p class="text-[11px] text-green-600 font-medium mt-0.5">Connected and operational</p>
                        </div>
                    </div>
                    <button type="button" class="text-[11px] font-bold text-primary uppercase tracking-widest hover:opacity-80 transition-opacity">Configure Keys</button>
                </div>
            </div>
        </section>

        {{-- Feature Toggles --}}
        <section class="space-y-6">
            <h3 class="font-poppins font-bold text-[11px] uppercase tracking-widest text-ink3 border-l-2 border-primary pl-4">Feature Flags</h3>
            <div class="grid grid-cols-1 gap-4">
                <div class="flex items-center justify-between p-6 bg-surface border border-rule group">
                    <div class="space-y-0.5">
                        <span class="text-[13px] font-bold text-ink font-body">Instructor Approvals</span>
                        <p class="text-[11px] text-ink3">Manual review required for new instructors</p>
                    </div>
                    <div class="w-10 h-5 bg-primary rounded-full relative cursor-pointer">
                        <div class="absolute right-1 top-1 w-3 h-3 bg-white rounded-full"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between p-6 bg-surface border border-rule group">
                    <div class="space-y-0.5">
                        <span class="text-[13px] font-bold text-ink font-body">Course Gamification</span>
                        <p class="text-[11px] text-ink3">Enable points and leaderboard system</p>
                    </div>
                    <div class="w-10 h-5 bg-rule rounded-full relative cursor-pointer">
                        <div class="absolute left-1 top-1 w-3 h-3 bg-white rounded-full"></div>
                    </div>
                </div>
            </div>
        </section>

        <div class="pt-8 border-t border-rule flex justify-end">
            <button type="submit" class="px-10 py-4 bg-primary text-white font-poppins font-bold text-xs uppercase tracking-widest hover:opacity-90 transition-opacity shadow-lg shadow-primary/20">
                Save All Changes
            </button>
        </div>
    </form>
</div>
@endsection
