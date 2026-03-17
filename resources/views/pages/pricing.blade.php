@extends('layouts.app')

@section('title', 'Pricing')

@section('content')
    <!-- Hero -->
    <section class="bg-ink text-white">
        <div class="max-w-7xl mx-auto px-6 py-20 lg:py-28 text-center">
            <span class="text-accent font-bold tracking-[0.2em] text-xs uppercase">Simple, Transparent Pricing</span>
            <h1 class="text-5xl lg:text-7xl font-extrabold leading-[1.1] mt-4 mb-6">
                Invest in your <span class="text-accent">future.</span>
            </h1>
            <p class="text-lg text-white/70 max-w-2xl mx-auto leading-relaxed">
                Choose the plan that fits your learning goals. Start free, upgrade when you're ready.
            </p>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="max-w-6xl mx-auto px-6 -mt-10 pb-20" x-data="{ cycle: 'monthly' }">
        <!-- Billing Toggle -->
        <div class="flex justify-center mb-12">
            <div class="inline-flex bg-surface border border-rule rounded-full p-1">
                <button @click="cycle = 'monthly'" :class="cycle === 'monthly' ? 'bg-ink text-white shadow-lg' : 'text-ink3'"
                    class="px-6 py-2.5 text-sm font-bold rounded-full transition-all">Monthly</button>
                <button @click="cycle = 'yearly'" :class="cycle === 'yearly' ? 'bg-ink text-white shadow-lg' : 'text-ink3'"
                    class="px-6 py-2.5 text-sm font-bold rounded-full transition-all">
                    Yearly <span class="text-green-500 text-xs font-bold ml-1">Save 30%</span>
                </button>
            </div>
        </div>

        @if($plans->isNotEmpty())
            {{-- Database-driven plans --}}
            <div class="grid grid-cols-1 md:grid-cols-{{ min($plans->count(), 3) }} gap-8">
                @foreach($plans as $plan)
                    <div class="bg-surface border {{ $plan->sort_order === 1 ? 'border-accent ring-2 ring-accent/20' : 'border-rule' }} rounded-2xl p-8 flex flex-col relative">
                        @if($plan->sort_order === 1)
                            <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-accent text-white text-xs font-bold rounded-full tracking-wider uppercase">Most Popular</span>
                        @endif
                        <h3 class="font-display font-bold text-xl text-ink mb-2">{{ $plan->name }}</h3>
                        <p class="text-sm text-ink3 mb-6">{{ $plan->description }}</p>
                        <div class="mb-6">
                            <template x-if="cycle === 'monthly'">
                                <div>
                                    <span class="font-display font-extrabold text-4xl text-ink">{{ $plan->formattedMonthlyPrice() }}</span>
                                    <span class="text-sm text-ink3">/month</span>
                                </div>
                            </template>
                            <template x-if="cycle === 'yearly'">
                                <div>
                                    @if($plan->price_yearly)
                                        <span class="font-display font-extrabold text-4xl text-ink">{{ $plan->formattedYearlyPrice() }}</span>
                                        <span class="text-sm text-ink3">/year</span>
                                        @if($plan->yearlyMonthlySavings())
                                            <span class="ml-2 text-xs font-bold text-green-600">Save {{ $plan->yearlyMonthlySavings() }}%</span>
                                        @endif
                                    @else
                                        <span class="font-display font-extrabold text-4xl text-ink">{{ $plan->formattedMonthlyPrice() }}</span>
                                        <span class="text-sm text-ink3">/month</span>
                                    @endif
                                </div>
                            </template>
                        </div>
                        <ul class="space-y-3 mb-8 flex-1">
                            @foreach($plan->features ?? [] as $feature)
                                <li class="flex items-start gap-2 text-sm text-ink2">
                                    <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                                    {{ $feature }}
                                </li>
                            @endforeach
                            @if($plan->course_limit)
                                <li class="flex items-start gap-2 text-sm text-ink2">
                                    <span class="material-symbols-outlined text-accent text-[18px] mt-0.5">info</span>
                                    Up to {{ $plan->course_limit }} courses
                                </li>
                            @else
                                <li class="flex items-start gap-2 text-sm text-ink2">
                                    <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">all_inclusive</span>
                                    Unlimited courses
                                </li>
                            @endif
                        </ul>
                        @auth
                            <form method="POST" action="{{ route('subscription.subscribe', $plan) }}">
                                @csrf
                                <input type="hidden" name="billing_cycle" x-bind:value="cycle">
                                <button type="submit"
                                    class="w-full py-3.5 {{ $plan->sort_order === 1 ? 'bg-accent' : 'bg-ink' }} text-white font-display font-bold text-sm rounded-xl hover:opacity-90 transition-opacity">
                                    Get Started
                                </button>
                            </form>
                        @else
                            <a href="{{ route('register') }}" class="w-full py-3.5 {{ $plan->sort_order === 1 ? 'bg-accent' : 'bg-ink' }} text-white font-display font-bold text-sm rounded-xl hover:opacity-90 transition-opacity text-center block">
                                Sign Up Free
                            </a>
                        @endauth
                    </div>
                @endforeach
            </div>
        @else
            {{-- Static fallback plans --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Free -->
                <div class="bg-surface border border-rule rounded-2xl p-8 flex flex-col">
                    <h3 class="font-display font-bold text-xl text-ink mb-2">Free</h3>
                    <p class="text-sm text-ink3 mb-6">Get started with free courses and basic features.</p>
                    <div class="mb-6">
                        <span class="font-display font-extrabold text-4xl text-ink">$0</span>
                        <span class="text-sm text-ink3">/forever</span>
                    </div>
                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            Access to free courses
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            Progress tracking
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            Course certificates
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            Community access
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="w-full py-3.5 bg-ink text-white font-display font-bold text-sm rounded-xl hover:opacity-90 transition-opacity text-center block">
                        Get Started
                    </a>
                </div>

                <!-- Pro -->
                <div class="bg-surface border border-accent ring-2 ring-accent/20 rounded-2xl p-8 flex flex-col relative">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-accent text-white text-xs font-bold rounded-full tracking-wider uppercase">Most Popular</span>
                    <h3 class="font-display font-bold text-xl text-ink mb-2">Pro</h3>
                    <p class="text-sm text-ink3 mb-6">Full access to premium courses and features.</p>
                    <div class="mb-6">
                        <template x-if="cycle === 'monthly'">
                            <div>
                                <span class="font-display font-extrabold text-4xl text-ink">$29</span>
                                <span class="text-sm text-ink3">/month</span>
                            </div>
                        </template>
                        <template x-if="cycle === 'yearly'">
                            <div>
                                <span class="font-display font-extrabold text-4xl text-ink">$245</span>
                                <span class="text-sm text-ink3">/year</span>
                                <span class="ml-2 text-xs font-bold text-green-600">Save 30%</span>
                            </div>
                        </template>
                    </div>
                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            All Free features
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            Unlimited premium courses
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            Downloadable resources
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            Priority support
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            Quizzes &amp; assessments
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="w-full py-3.5 bg-accent text-white font-display font-bold text-sm rounded-xl hover:opacity-90 transition-opacity text-center block">
                        Start Pro Trial
                    </a>
                </div>

                <!-- Enterprise -->
                <div class="bg-surface border border-rule rounded-2xl p-8 flex flex-col">
                    <h3 class="font-display font-bold text-xl text-ink mb-2">Enterprise</h3>
                    <p class="text-sm text-ink3 mb-6">For teams and organizations that need custom solutions.</p>
                    <div class="mb-6">
                        <span class="font-display font-extrabold text-4xl text-ink">Custom</span>
                    </div>
                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            All Pro features
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            Custom branding &amp; domain
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            Team management &amp; analytics
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            SSO / SAML integration
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            Dedicated account manager
                        </li>
                        <li class="flex items-start gap-2 text-sm text-ink2">
                            <span class="material-symbols-outlined text-green-500 text-[18px] mt-0.5">check_circle</span>
                            API access &amp; webhooks
                        </li>
                    </ul>
                    <a href="{{ route('pages.contact') }}" class="w-full py-3.5 bg-ink text-white font-display font-bold text-sm rounded-xl hover:opacity-90 transition-opacity text-center block">
                        Contact Sales
                    </a>
                </div>
            </div>
        @endif
    </section>

    <!-- FAQ -->
    <section class="border-t border-rule bg-surface">
        <div class="max-w-3xl mx-auto px-6 py-20">
            <h2 class="font-display font-extrabold text-3xl text-ink text-center mb-12">Frequently Asked Questions</h2>

            <div class="space-y-4" x-data="{ open: null }">
                @php
                    $faqs = [
                        ['q' => 'Can I switch plans later?', 'a' => 'Yes, you can upgrade or downgrade your plan at any time. Changes take effect at the start of your next billing cycle.'],
                        ['q' => 'Is there a free trial?', 'a' => 'All accounts start with free access to selected courses. Paid plans include a 7-day money-back guarantee so you can try risk-free.'],
                        ['q' => 'What payment methods do you accept?', 'a' => 'We accept all major credit cards (Visa, MasterCard, Amex), PayPal, Paystack, and Flutterwave for African markets. Enterprise customers can pay via invoice.'],
                        ['q' => 'Can I cancel my subscription?', 'a' => 'Absolutely. Cancel anytime from your account settings. You\'ll retain access until the end of your current billing period.'],
                        ['q' => 'Do I get certificates?', 'a' => 'Yes! All plans include verifiable certificates upon course completion. Each certificate has a unique verification URL you can share with employers.'],
                        ['q' => 'Are courses available offline?', 'a' => 'Pro and Enterprise plans include downloadable resources. Video content requires an internet connection for content protection.'],
                    ];
                @endphp

                @foreach($faqs as $i => $faq)
                    <div class="border border-rule rounded-xl overflow-hidden">
                        <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                                class="w-full flex items-center justify-between p-5 text-left hover:bg-bg/50 transition-colors">
                            <span class="font-display font-bold text-[15px] text-ink pr-4">{{ $faq['q'] }}</span>
                            <span class="material-symbols-outlined text-ink3 text-[20px] transition-transform shrink-0"
                                  :class="open === {{ $i }} && 'rotate-180'">expand_more</span>
                        </button>
                        <div x-show="open === {{ $i }}" x-transition class="px-5 pb-5">
                            <p class="text-sm text-ink2 leading-relaxed">{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Bottom CTA -->
    <section class="max-w-7xl mx-auto px-6 py-20">
        <div class="bg-ink p-12 lg:p-20 text-center">
            <h2 class="text-4xl lg:text-5xl font-bold font-display text-white leading-tight mb-4">Still deciding?</h2>
            <p class="text-white/80 text-lg max-w-xl mx-auto mb-8">Start with free courses and upgrade when you're ready. No credit card required.</p>
            <a href="{{ route('register') }}" class="inline-block bg-accent text-white font-bold px-10 py-4 rounded-card hover:opacity-90 transition-opacity text-base">
                Create Free Account
            </a>
        </div>
    </section>
@endsection
