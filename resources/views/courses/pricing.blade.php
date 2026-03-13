@extends('layouts.app')

@section('title', 'Pricing Plans')

@section('content')
<section class="py-24 px-6 max-w-6xl mx-auto text-center">
    <div class="flex items-center justify-center gap-4 mb-6">
        <div class="w-5 h-[1px] bg-accent"></div>
        <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-accent">Simple Pricing</span>
    </div>
    
    <h1 class="font-display font-extrabold text-[44px] leading-tight text-ink mb-8">
        Choose the way <br/>
        you <span class="text-accent underline decoration-rule decoration-2 underline-offset-8">learn.</span>
    </h1>

    <p class="max-w-md mx-auto text-ink2 font-body text-sm mb-16">
        Unlock your potential with plans designed for individuals and teams alike. No hidden fees.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        {{-- Free Plan --}}
        <div class="bg-surface border border-rule rounded-card p-10 flex flex-col items-start transition-all duration-150 hover:border-ink">
            <span class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Free</span>
            <div class="flex items-baseline gap-1 mb-8">
                <span class="font-display font-extrabold text-4xl text-ink">$0</span>
            </div>
            
            <ul class="space-y-4 text-left mb-10 w-full">
                <li class="flex items-center gap-3 text-sm text-ink2 font-body">
                    <span class="text-success text-lg">✓</span> Access to free courses
                </li>
                <li class="flex items-center gap-3 text-sm text-ink3 font-body">
                    <span class="text-ink3 text-lg">—</span> Community support
                </li>
            </ul>

            <a href="{{ url('/register') }}" class="w-full py-3.5 border border-rule rounded-card font-display font-bold text-sm text-ink hover:border-ink transition-colors mt-auto">Get Started</a>
        </div>

        {{-- Monthly Plan --}}
        <div class="relative bg-surface border-2 border-accent rounded-card p-10 flex flex-col items-start">
            <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-accent-bg border border-accent/20 rounded-pill">
                <span class="text-[10px] font-bold uppercase tracking-widest text-accent font-display">Most Popular</span>
            </div>

            <span class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Monthly</span>
            <div class="flex items-baseline gap-1 mb-8">
                <span class="font-display font-extrabold text-4xl text-ink">$19</span>
                <span class="text-ink3 text-xs font-body">/ month</span>
            </div>
            
            <ul class="space-y-4 text-left mb-10 w-full">
                <li class="flex items-center gap-3 text-sm text-ink2 font-body">
                    <span class="text-success text-lg">✓</span> All Premium Courses
                </li>
                <li class="flex items-center gap-3 text-sm text-ink2 font-body">
                    <span class="text-success text-lg">✓</span> Lesson Downloads
                </li>
                <li class="flex items-center gap-3 text-sm text-ink2 font-body">
                    <span class="text-success text-lg">✓</span> Certificates of Completion
                </li>
            </ul>

            <a href="{{ url('/register') }}" class="w-full py-3.5 bg-accent text-white rounded-card font-display font-bold text-sm hover:opacity-90 transition-opacity mt-auto">Subscribe Now</a>
        </div>

        {{-- Annual Plan --}}
        <div class="bg-surface border border-rule rounded-card p-10 flex flex-col items-start transition-all duration-150 hover:border-ink">
            <span class="text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Annual</span>
            <div class="flex items-baseline gap-1 mb-8">
                <span class="font-display font-extrabold text-4xl text-ink">$149</span>
                <span class="text-ink3 text-xs font-body">/ year</span>
            </div>
            
            <ul class="space-y-4 text-left mb-10 w-full">
                <li class="flex items-center gap-3 text-sm text-ink2 font-body">
                    <span class="text-success text-lg">✓</span> Everything in Monthly
                </li>
                <li class="flex items-center gap-3 text-sm text-ink2 font-body">
                    <span class="text-success text-lg">✓</span> Priority Support
                </li>
                <li class="flex items-center gap-3 text-sm text-ink2 font-body">
                    <span class="text-success text-lg">✓</span> 2 Months Free
                </li>
            </ul>

            <a href="{{ url('/register') }}" class="w-full py-3.5 border border-rule rounded-card font-display font-bold text-sm text-ink hover:border-ink transition-colors mt-auto">Choose Plan</a>
        </div>
    </div>
</section>
@endsection
