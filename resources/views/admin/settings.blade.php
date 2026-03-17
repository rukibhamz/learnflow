@extends('layouts.admin')

@section('title', 'Admin Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-8" x-data="{ activeTab: 'general' }">
    <!-- Breadcrumbs & Header -->
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <nav class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2 font-poppins">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors uppercase">Admin</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-ink uppercase">Settings</span>
            </nav>
            <h1 class="font-poppins font-bold text-2xl tracking-tight text-ink">Admin Settings</h1>
            <p class="text-[13px] font-body text-ink2">Configure your LearnFlow LMS platform preferences.</p>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-rule flex items-center gap-10">
        <button @click="activeTab = 'general'" 
                :class="activeTab === 'general' ? 'text-primary border-primary' : 'text-ink3 border-transparent hover:text-ink hover:border-rule'"
                class="pb-4 font-poppins font-bold text-[13px] border-b-2 transition-all outline-none">
            General
        </button>
        <button @click="activeTab = 'email'" 
                :class="activeTab === 'email' ? 'text-primary border-primary' : 'text-ink3 border-transparent hover:text-ink hover:border-rule'"
                class="pb-4 font-poppins font-bold text-[13px] border-b-2 transition-all outline-none">
            Email
        </button>
        <button @click="activeTab = 'payment'" 
                :class="activeTab === 'payment' ? 'text-primary border-primary' : 'text-ink3 border-transparent hover:text-ink hover:border-rule'"
                class="pb-4 font-poppins font-bold text-[13px] border-b-2 transition-all outline-none">
            Payment
        </button>
        <button @click="activeTab = 'enrollment'" 
                :class="activeTab === 'enrollment' ? 'text-primary border-primary' : 'text-ink3 border-transparent hover:text-ink hover:border-rule'"
                class="pb-4 font-poppins font-bold text-[13px] border-b-2 transition-all outline-none">
            Enrollment
        </button>
        <button @click="activeTab = 'notifications'" 
                :class="activeTab === 'notifications' ? 'text-primary border-primary' : 'text-ink3 border-transparent hover:text-ink hover:border-rule'"
                class="pb-4 font-poppins font-bold text-[13px] border-b-2 transition-all outline-none">
            Notifications
        </button>
    </div>

    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-100 rounded-lg text-sm text-green-800 font-sans flex items-center gap-3">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="p-4 bg-red-50 border border-red-100 rounded-lg text-sm text-red-800 font-sans flex items-center gap-3">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-surface border border-rule rounded-xl overflow-hidden shadow-sm">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            
            <!-- General Tab -->
            <div x-show="activeTab === 'general'" class="p-10 space-y-12">
                <div class="space-y-10">
                    <h3 class="font-poppins font-bold text-[11px] uppercase tracking-widest text-ink3 border-b border-rule pb-3">General Configuration</h3>
                    
                    <div class="space-y-8">
                        <!-- Site Name -->
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Site Name</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">This name will appear in the browser tab and email footers.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="text" name="site_name" value="{{ \App\Models\Setting::get('site_name', config('app.name')) }}" 
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="LearnFlow Academy">
                            </div>
                        </div>
                        
                        <!-- Support Email -->
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Support Email</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">The primary contact address for student inquiries.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="email" name="support_email" value="{{ \App\Models\Setting::get('support_email', 'support@learnflow.ai') }}" 
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="support@learnflow.edu">
                            </div>
                        </div>

                        <!-- Timezone -->
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Timezone</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Used for scheduling live classes and reporting.</p>
                            </div>
                            <div class="md:w-1/2">
                                <select name="timezone" class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none cursor-pointer">
                                    <option value="UTC" {{ \App\Models\Setting::get('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ \App\Models\Setting::get('timezone') == 'America/New_York' ? 'selected' : '' }}>Eastern Time (US & Canada)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-10 pt-10 border-t border-rule">
                    <h3 class="font-poppins font-bold text-[11px] uppercase tracking-widest text-ink3 border-b border-rule pb-3">Status & Maintenance</h3>
                    
                    <div class="space-y-8">
                         <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <span class="text-[13px] font-bold text-ink font-poppins">Maintenance Mode</span>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Disable front-end access while updating.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="maintenance_mode" value="0">
                                <input type="checkbox" name="maintenance_mode" value="1" {{ \App\Models\Setting::get('maintenance_mode') ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Tab -->
            <div x-show="activeTab === 'email'" class="p-10 space-y-12">
                <div class="space-y-10" x-data="{ mailer: '{{ \App\Models\Setting::get('mail_mailer', config('mail.default')) }}' }">
                    <div class="flex items-center justify-between border-b border-rule pb-3">
                        <h3 class="font-poppins font-bold text-[11px] uppercase tracking-widest text-ink3">SMTP Settings</h3>
                        <button type="button" @click="$dispatch('open-modal', 'test-email-modal')" class="text-[10px] font-bold text-primary uppercase tracking-widest hover:opacity-80 transition-opacity font-poppins">Send Test Email</button>
                    </div>

                    <div class="space-y-8">
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Mail Mailer</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Primary driver used for sending system emails.</p>
                            </div>
                            <div class="md:w-1/2">
                                <select name="mail_mailer" x-model="mailer" class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none">
                                    <option value="smtp">SMTP</option>
                                    <option value="ses">Amazon SES</option>
                                    <option value="mailgun">Mailgun</option>
                                    <option value="postmark">Postmark</option>
                                    <option value="log">Log</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Encryption</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Security protocol for mail transmission.</p>
                            </div>
                            <div class="md:w-1/2">
                                <select name="mail_encryption" class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none">
                                    <option value="tls" {{ \App\Models\Setting::get('mail_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ \App\Models\Setting::get('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="null" {{ \App\Models\Setting::get('mail_encryption') == 'null' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                        </div>

                        <template x-if="mailer === 'smtp'">
                            <div class="space-y-8 pt-6 border-t border-rule/50">
                                <div class="flex flex-col md:flex-row md:items-start gap-6">
                                    <div class="md:w-1/2 space-y-1">
                                        <label class="block text-[13px] font-bold text-ink font-poppins">SMTP Host</label>
                                        <p class="text-[12px] text-ink3 font-sans leading-relaxed">Your mail server address.</p>
                                    </div>
                                    <div class="md:w-1/2">
                                        <input type="text" name="mail_host" value="{{ \App\Models\Setting::get('mail_host', config('mail.mailers.smtp.host')) }}" 
                                               class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="smtp.postmarkapp.com">
                                    </div>
                                </div>
                                <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                                    <div class="md:w-1/2 space-y-1">
                                        <label class="block text-[13px] font-bold text-ink font-poppins">SMTP Port</label>
                                        <p class="text-[12px] text-ink3 font-sans leading-relaxed">Outgoing port for your SMTP server.</p>
                                    </div>
                                    <div class="md:w-1/2">
                                        <input type="text" name="mail_port" value="{{ \App\Models\Setting::get('mail_port', config('mail.mailers.smtp.port')) }}" 
                                               class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="587">
                                    </div>
                                </div>
                                <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                                    <div class="md:w-1/2 space-y-1">
                                        <label class="block text-[13px] font-bold text-ink font-poppins">SMTP Username</label>
                                        <p class="text-[12px] text-ink3 font-sans leading-relaxed">Your SMTP server username.</p>
                                    </div>
                                    <div class="md:w-1/2">
                                        <input type="text" name="mail_username" value="{{ \App\Models\Setting::get('mail_username', config('mail.mailers.smtp.username')) }}" 
                                               class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="username@example.com">
                                    </div>
                                </div>
                                <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                                    <div class="md:w-1/2 space-y-1">
                                        <label class="block text-[13px] font-bold text-ink font-poppins">SMTP Password</label>
                                        <p class="text-[12px] text-ink3 font-sans leading-relaxed">Your SMTP server password.</p>
                                    </div>
                                    <div class="md:w-1/2">
                                        <input type="password" name="mail_password" value="{{ \App\Models\Setting::get('mail_password', config('mail.mailers.smtp.password')) }}" 
                                               class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="••••••••••••">
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="space-y-8 pt-8 border-t border-rule">
                            <h3 class="font-poppins font-bold text-[11px] uppercase tracking-widest text-ink3 border-rule pb-3">Sender Configuration</h3>
                            
                            <div class="flex flex-col md:flex-row md:items-start gap-6">
                                <div class="md:w-1/2 space-y-1">
                                    <label class="block text-[13px] font-bold text-ink font-poppins">From Address</label>
                                    <p class="text-[12px] text-ink3 font-sans leading-relaxed">The email address system emails are sent from.</p>
                                </div>
                                <div class="md:w-1/2">
                                    <input type="email" name="mail_from_address" value="{{ \App\Models\Setting::get('mail_from_address', config('mail.from.address')) }}" 
                                           class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="noreply@learnflow.ai">
                                </div>
                            </div>

                            <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                                <div class="md:w-1/2 space-y-1">
                                    <label class="block text-[13px] font-bold text-ink font-poppins">From Name</label>
                                    <p class="text-[12px] text-ink3 font-sans leading-relaxed">The name system emails are sent from.</p>
                                </div>
                                <div class="md:w-1/2">
                                    <input type="text" name="mail_from_name" value="{{ \App\Models\Setting::get('mail_from_name', config('mail.from.name')) }}" 
                                           class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="LearnFlow Academy">
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-8 border-t border-rule">
                            <div class="space-y-1">
                                <span class="text-[13px] font-bold text-ink font-poppins">Enable SSL/TLS</span>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Encrypt connection between LMS and mail server.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="mail_use_ssl" value="0">
                                <input type="checkbox" name="mail_use_ssl" value="1" {{ \App\Models\Setting::get('mail_use_ssl') ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Tab -->
            <div x-show="activeTab === 'payment'" class="p-10 space-y-12"
                 x-data="{
                    stripe: {{ \App\Models\Setting::get('stripe_enabled', '0') ? 'true' : 'false' }},
                    paypal: {{ \App\Models\Setting::get('paypal_enabled', '0') ? 'true' : 'false' }},
                    paystack: {{ \App\Models\Setting::get('paystack_enabled', '0') ? 'true' : 'false' }},
                    flutterwave: {{ \App\Models\Setting::get('flutterwave_enabled', '0') ? 'true' : 'false' }},
                    razorpay: {{ \App\Models\Setting::get('razorpay_enabled', '0') ? 'true' : 'false' }}
                 }">

                {{-- Default Gateway --}}
                <div class="space-y-6">
                    <h3 class="font-poppins font-bold text-[11px] uppercase tracking-widest text-ink3 border-b border-rule pb-3">Default Payment Gateway</h3>
                    <div class="flex flex-col md:flex-row md:items-start gap-6">
                        <div class="md:w-1/2 space-y-1">
                            <label class="block text-[13px] font-bold text-ink font-poppins">Primary Gateway</label>
                            <p class="text-[12px] text-ink3 font-sans leading-relaxed">The default gateway used at checkout. Students will see this option first.</p>
                        </div>
                        <div class="md:w-1/2">
                            <select name="default_payment_gateway" class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none cursor-pointer">
                                @php $defaultGw = \App\Models\Setting::get('default_payment_gateway', 'stripe'); @endphp
                                <option value="stripe" {{ $defaultGw === 'stripe' ? 'selected' : '' }}>Stripe</option>
                                <option value="paypal" {{ $defaultGw === 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="paystack" {{ $defaultGw === 'paystack' ? 'selected' : '' }}>Paystack</option>
                                <option value="flutterwave" {{ $defaultGw === 'flutterwave' ? 'selected' : '' }}>Flutterwave</option>
                                <option value="razorpay" {{ $defaultGw === 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                        <div class="md:w-1/2 space-y-1">
                            <label class="block text-[13px] font-bold text-ink font-poppins">Currency</label>
                            <p class="text-[12px] text-ink3 font-sans leading-relaxed">The default currency for all transactions on the platform.</p>
                        </div>
                        <div class="md:w-1/2">
                            <select name="payment_currency" class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none cursor-pointer">
                                @php $currency = \App\Models\Setting::get('payment_currency', 'USD'); @endphp
                                <option value="USD" {{ $currency === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ $currency === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                <option value="GBP" {{ $currency === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                <option value="NGN" {{ $currency === 'NGN' ? 'selected' : '' }}>NGN - Nigerian Naira</option>
                                <option value="GHS" {{ $currency === 'GHS' ? 'selected' : '' }}>GHS - Ghanaian Cedi</option>
                                <option value="KES" {{ $currency === 'KES' ? 'selected' : '' }}>KES - Kenyan Shilling</option>
                                <option value="ZAR" {{ $currency === 'ZAR' ? 'selected' : '' }}>ZAR - South African Rand</option>
                                <option value="INR" {{ $currency === 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                <option value="CAD" {{ $currency === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                <option value="AUD" {{ $currency === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ===== STRIPE ===== --}}
                <div class="space-y-6 pt-4 border-t border-rule">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-[#635BFF]/10 flex items-center justify-center rounded-lg">
                                <span class="font-poppins font-black text-[13px] text-[#635BFF]">S</span>
                            </div>
                            <div class="space-y-0.5">
                                <h3 class="font-poppins font-bold text-[14px] text-ink">Stripe</h3>
                                <p class="text-[11px] text-ink3 font-sans">Cards, Apple Pay, Google Pay &mdash; 135+ currencies</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="stripe_enabled" value="0">
                            <input type="checkbox" name="stripe_enabled" value="1" x-model="stripe" class="sr-only peer">
                            <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#635BFF]"></div>
                        </label>
                    </div>
                    <div x-show="stripe" x-transition class="space-y-8 pl-14">
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Publishable Key</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Used for client-side tokenization.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="text" name="stripe_publishable_key" value="{{ \App\Models\Setting::get('stripe_publishable_key') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="pk_live_...">
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Secret Key</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Used for server-side API requests.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="password" name="stripe_secret_key" value="{{ \App\Models\Setting::get('stripe_secret_key') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="sk_live_...">
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Webhook Secret</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Securely verify Stripe webhooks.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="password" name="stripe_webhook_secret" value="{{ \App\Models\Setting::get('stripe_webhook_secret') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="whsec_...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== PAYPAL ===== --}}
                <div class="space-y-6 pt-4 border-t border-rule">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-[#003087]/10 flex items-center justify-center rounded-lg">
                                <span class="font-poppins font-black text-[13px] text-[#003087]">PP</span>
                            </div>
                            <div class="space-y-0.5">
                                <h3 class="font-poppins font-bold text-[14px] text-ink">PayPal</h3>
                                <p class="text-[11px] text-ink3 font-sans">PayPal balance, cards, Pay Later &mdash; 200+ markets</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="paypal_enabled" value="0">
                            <input type="checkbox" name="paypal_enabled" value="1" x-model="paypal" class="sr-only peer">
                            <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003087]"></div>
                        </label>
                    </div>
                    <div x-show="paypal" x-transition class="space-y-8 pl-14">
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Mode</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Sandbox for testing, Live for production transactions.</p>
                            </div>
                            <div class="md:w-1/2">
                                <select name="paypal_mode" class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none cursor-pointer">
                                    @php $ppMode = \App\Models\Setting::get('paypal_mode', 'sandbox'); @endphp
                                    <option value="sandbox" {{ $ppMode === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                    <option value="live" {{ $ppMode === 'live' ? 'selected' : '' }}>Live</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Client ID</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">From your PayPal Developer application credentials.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="text" name="paypal_client_id" value="{{ \App\Models\Setting::get('paypal_client_id') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="AZ...">
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Client Secret</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Secret key from your PayPal Developer application.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="password" name="paypal_client_secret" value="{{ \App\Models\Setting::get('paypal_client_secret') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="EG...">
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Webhook ID</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Used to verify PayPal webhook event signatures.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="text" name="paypal_webhook_id" value="{{ \App\Models\Setting::get('paypal_webhook_id') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="WH-...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== PAYSTACK ===== --}}
                <div class="space-y-6 pt-4 border-t border-rule">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-[#00C3F7]/10 flex items-center justify-center rounded-lg">
                                <span class="font-poppins font-black text-[13px] text-[#00796B]">PS</span>
                            </div>
                            <div class="space-y-0.5">
                                <h3 class="font-poppins font-bold text-[14px] text-ink">Paystack</h3>
                                <p class="text-[11px] text-ink3 font-sans">Cards, bank transfers, mobile money &mdash; Nigeria, Ghana, South Africa, Kenya</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="paystack_enabled" value="0">
                            <input type="checkbox" name="paystack_enabled" value="1" x-model="paystack" class="sr-only peer">
                            <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#00796B]"></div>
                        </label>
                    </div>
                    <div x-show="paystack" x-transition class="space-y-8 pl-14">
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Public Key</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Used to initialize Paystack inline checkout.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="text" name="paystack_public_key" value="{{ \App\Models\Setting::get('paystack_public_key') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="pk_live_...">
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Secret Key</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Used for server-side API calls to Paystack.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="password" name="paystack_secret_key" value="{{ \App\Models\Setting::get('paystack_secret_key') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="sk_live_...">
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Webhook Secret</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Validate incoming Paystack webhook payloads.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="password" name="paystack_webhook_secret" value="{{ \App\Models\Setting::get('paystack_webhook_secret') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="whsec_...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== FLUTTERWAVE ===== --}}
                <div class="space-y-6 pt-4 border-t border-rule">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-[#F5A623]/10 flex items-center justify-center rounded-lg">
                                <span class="font-poppins font-black text-[13px] text-[#F5A623]">FW</span>
                            </div>
                            <div class="space-y-0.5">
                                <h3 class="font-poppins font-bold text-[14px] text-ink">Flutterwave</h3>
                                <p class="text-[11px] text-ink3 font-sans">Cards, bank transfers, M-Pesa, mobile money &mdash; 34 African countries</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="flutterwave_enabled" value="0">
                            <input type="checkbox" name="flutterwave_enabled" value="1" x-model="flutterwave" class="sr-only peer">
                            <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#F5A623]"></div>
                        </label>
                    </div>
                    <div x-show="flutterwave" x-transition class="space-y-8 pl-14">
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Public Key</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Used to initialize Flutterwave inline checkout.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="text" name="flutterwave_public_key" value="{{ \App\Models\Setting::get('flutterwave_public_key') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="FLWPUBK-...">
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Secret Key</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Used for server-side verification and API calls.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="password" name="flutterwave_secret_key" value="{{ \App\Models\Setting::get('flutterwave_secret_key') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="FLWSECK-...">
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Encryption Key</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Used to encrypt payment payloads for extra security.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="password" name="flutterwave_encryption_key" value="{{ \App\Models\Setting::get('flutterwave_encryption_key') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="FLWSECK_...">
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Webhook Hash</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Secret hash to verify Flutterwave webhook signatures.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="password" name="flutterwave_webhook_hash" value="{{ \App\Models\Setting::get('flutterwave_webhook_hash') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="your-secret-hash">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== RAZORPAY ===== --}}
                <div class="space-y-6 pt-4 border-t border-rule">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-[#072654]/10 flex items-center justify-center rounded-lg">
                                <span class="font-poppins font-black text-[13px] text-[#072654]">RZ</span>
                            </div>
                            <div class="space-y-0.5">
                                <h3 class="font-poppins font-bold text-[14px] text-ink">Razorpay</h3>
                                <p class="text-[11px] text-ink3 font-sans">Cards, UPI, NetBanking, wallets &mdash; India</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="razorpay_enabled" value="0">
                            <input type="checkbox" name="razorpay_enabled" value="1" x-model="razorpay" class="sr-only peer">
                            <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#072654]"></div>
                        </label>
                    </div>
                    <div x-show="razorpay" x-transition class="space-y-8 pl-14">
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Key ID</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Your Razorpay API Key ID from the dashboard.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="text" name="razorpay_key_id" value="{{ \App\Models\Setting::get('razorpay_key_id') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="rzp_live_...">
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Key Secret</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Your Razorpay API Key Secret for server-side operations.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="password" name="razorpay_key_secret" value="{{ \App\Models\Setting::get('razorpay_key_secret') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="••••••••••••">
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-start gap-6 pt-6 border-t border-rule/50">
                            <div class="md:w-1/2 space-y-1">
                                <label class="block text-[13px] font-bold text-ink font-poppins">Webhook Secret</label>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Used to verify webhook signatures from Razorpay.</p>
                            </div>
                            <div class="md:w-1/2">
                                <input type="password" name="razorpay_webhook_secret" value="{{ \App\Models\Setting::get('razorpay_webhook_secret') }}"
                                       class="w-full h-12 border border-rule rounded-lg px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow" placeholder="whsec_...">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Enrollment Tab -->
            <div x-show="activeTab === 'enrollment'" class="p-10 space-y-12">
                <div class="space-y-10">
                    <h3 class="font-poppins font-bold text-[11px] uppercase tracking-widest text-ink3 border-b border-rule pb-3">Enrollment & Platform Features</h3>
                    
                    <div class="space-y-8">
                        @php $instructorApprovals = \App\Models\Setting::get('feature_instructor_approvals', '1'); @endphp
                        <div class="flex items-center justify-between">
                            <div class="space-y-1">
                                <span class="text-[13px] font-bold text-ink font-poppins">Instructor Approvals</span>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Manual review required for new instructors</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="feature_instructor_approvals" value="0">
                                <input type="checkbox" name="feature_instructor_approvals" value="1" {{ $instructorApprovals ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>

                         @php $gamification = \App\Models\Setting::get('feature_gamification', '0'); @endphp
                         <div class="flex items-center justify-between pt-6 border-t border-rule/50">
                            <div class="space-y-1">
                                <span class="text-[13px] font-bold text-ink font-poppins">Course Gamification</span>
                                <p class="text-[12px] text-ink3 font-sans leading-relaxed">Enable points and leaderboard system</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="feature_gamification" value="0">
                                <input type="checkbox" name="feature_gamification" value="1" {{ $gamification ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Tab -->
            <div x-show="activeTab === 'notifications'" class="p-10 space-y-12">
                <div class="flex items-center justify-center p-12 text-ink3 font-sans text-[13px] italic">
                    Notification settings coming soon...
                </div>
            </div>

            <!-- Footer -->
            <div class="px-10 py-6 bg-background-light/30 border-t border-rule flex items-center justify-end gap-6">
                <div x-show="{{ session('success') ? 'true' : 'false' }}" x-transition class="text-[12px] text-success font-bold flex items-center gap-1.5 font-sans">
                    <span class="material-symbols-outlined text-[16px]">done</span> Saved
                </div>
                <button type="submit" class="px-10 py-3 bg-ink text-white rounded-lg font-poppins font-bold text-[12px] uppercase tracking-widest hover:bg-ink/90 transition-all shadow-lg shadow-black/5">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="p-8 bg-surface border border-rule rounded-xl space-y-5 shadow-sm">
            <div class="w-12 h-12 bg-primary/5 flex items-center justify-center rounded-xl">
                <span class="material-symbols-outlined text-primary text-[24px]">shield</span>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-[15px] text-ink font-poppins">System Health</h4>
                <p class="text-[12px] text-ink3 leading-relaxed font-sans">All services are running normally. Last sync was 5 minutes ago.</p>
            </div>
        </div>
        
        <div class="p-8 bg-surface border border-rule rounded-xl space-y-5 shadow-sm">
            <div class="w-12 h-12 bg-blue-500/5 flex items-center justify-center rounded-xl">
                <span class="material-symbols-outlined text-blue-600 text-[24px]">database</span>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-[15px] text-ink font-poppins">Storage Usage</h4>
                <p class="text-[12px] text-ink3 leading-relaxed font-sans">Using 4.2GB of 10GB available. Media assets are optimized.</p>
            </div>
        </div>

        <div class="p-8 bg-surface border border-rule rounded-xl space-y-5 shadow-sm">
            <div class="w-12 h-12 bg-amber-500/5 flex items-center justify-center rounded-xl">
                <span class="material-symbols-outlined text-amber-600 text-[24px]">history</span>
            </div>
            <div class="space-y-1">
                <h4 class="font-bold text-[15px] text-ink font-poppins">Audit Logs</h4>
                <p class="text-[12px] text-ink3 leading-relaxed font-sans">Last configuration change by Alex M. on Oct 24, 2023.</p>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div x-data="{ open: false }" 
     @open-modal.window="if ($event.detail === 'test-email-modal') open = true"
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink/40 backdrop-blur-sm"
     x-transition.opacity>
    <div @click.away="open = false" class="bg-surface w-full max-w-md rounded-2xl border border-rule shadow-2xl overflow-hidden" x-transition.scale.95>
        <div class="p-6 border-b border-rule flex items-center justify-between">
            <h3 class="font-poppins font-bold text-lg text-ink">Test Email Delivery</h3>
            <button @click="open = false" class="text-ink3 hover:text-ink transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.settings.test-email') }}" class="p-8 space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2 font-poppins">Recipient Email Address</label>
                <input type="email" name="test_email" value="{{ auth()->user()?->email }}" required
                       class="w-full h-12 border border-rule rounded-xl px-4 font-sans text-[14px] focus:ring-1 focus:ring-primary/30 outline-none transition-shadow">
            </div>
            <p class="text-[12px] text-ink3 leading-relaxed font-sans">
                This will send a live test message using your current SMTP/Mailer settings to verify configuration.
            </p>
            <div class="flex gap-4 pt-4">
                <button type="button" @click="open = false" class="flex-1 h-12 rounded-xl border border-rule font-poppins font-bold text-[11px] uppercase tracking-widest hover:bg-background-light transition-colors">
                    Cancel
                </button>
                <button type="submit" class="flex-1 h-12 bg-primary text-white rounded-xl font-poppins font-bold text-[11px] uppercase tracking-widest hover:opacity-90 transition-opacity shadow-lg shadow-primary/10">
                    Send Test
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
