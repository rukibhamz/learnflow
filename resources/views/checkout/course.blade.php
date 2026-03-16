@extends('layouts.app')

@section('title', 'Checkout - ' . $course->title)

@section('content')
<div class="min-h-screen bg-bg py-12">
    <div class="max-w-2xl mx-auto px-6">
        <div class="bg-surface border border-rule rounded-2xl overflow-hidden">
            {{-- Header --}}
            <div class="p-6 border-b border-rule">
                <h1 class="font-display font-bold text-xl text-ink">Complete Your Purchase</h1>
            </div>

            {{-- Course Summary --}}
            <div class="p-6 border-b border-rule">
                <div class="flex gap-4">
                    <div class="w-24 h-16 bg-bg rounded-lg overflow-hidden shrink-0">
                        @if($course->getFirstMediaUrl('thumbnail'))
                            <img src="{{ $course->getFirstMediaUrl('thumbnail', 'thumb') }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/10 to-primary/5">
                                <span class="material-symbols-outlined text-primary/30">school</span>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="font-display font-bold text-base text-ink line-clamp-2">{{ $course->title }}</h2>
                        <p class="text-xs text-ink3 mt-1">by {{ $course->instructor?->name }}</p>
                    </div>
                    <div class="shrink-0">
                        <span class="font-display font-bold text-xl text-ink">${{ number_format($course->price, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="p-6 border-b border-rule">
                <h3 class="font-display font-bold text-sm text-ink mb-4">Order Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-ink2">Course Price</span>
                        <span class="text-ink">${{ number_format($course->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-ink2">Discount</span>
                        <span class="text-ink">$0.00</span>
                    </div>
                    <div class="border-t border-rule pt-3 flex justify-between">
                        <span class="font-display font-bold text-ink">Total</span>
                        <span class="font-display font-bold text-xl text-ink">${{ number_format($course->price, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Section (Placeholder) --}}
            <div class="p-6">
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-center mb-6">
                    <span class="material-symbols-outlined text-[48px] text-amber-500 mb-3 block">construction</span>
                    <h3 class="font-display font-bold text-lg text-amber-800 mb-2">Payment Coming Soon</h3>
                    <p class="text-sm text-amber-700">Stripe Checkout integration will be available in the next phase. For now, please contact support for manual enrollment.</p>
                </div>

                <div class="flex gap-4">
                    <a href="{{ route('courses.show', $course->slug) }}" class="flex-1 py-3 border border-rule text-ink2 font-medium text-sm text-center rounded-xl hover:bg-bg transition-colors">
                        Back to Course
                    </a>
                    <button disabled class="flex-1 py-3 bg-ink/50 text-white font-display font-bold text-sm rounded-xl cursor-not-allowed">
                        Pay with Stripe
                    </button>
                </div>
            </div>
        </div>

        {{-- Trust Badges --}}
        <div class="mt-8 flex items-center justify-center gap-8 text-xs text-ink3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">lock</span>
                Secure Payment
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                30-Day Guarantee
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">support_agent</span>
                24/7 Support
            </div>
        </div>
    </div>
</div>
@endsection
