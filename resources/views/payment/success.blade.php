@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="min-h-screen bg-bg py-12">
    <div class="max-w-xl mx-auto px-6">
        <div class="bg-surface border border-rule rounded-2xl p-8 text-center">
            <span class="material-symbols-outlined text-[56px] text-green-500 mb-4">check_circle</span>
            <h1 class="font-display font-extrabold text-2xl text-ink mb-2">You're enrolled!</h1>
            <p class="text-sm text-ink3 mb-6">
                Your payment for <strong>{{ $order->course->title }}</strong> was successful.
            </p>

            <a href="{{ route('learn.show', $order->course->slug) }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90 transition-opacity">
                Start Learning
            </a>

            <p class="text-xs text-ink3 mt-6">
                You can access your receipt and invoice from the <a href="{{ route('my-orders') }}" class="text-primary underline">My Orders</a> page.
            </p>
        </div>
    </div>
</div>
@endsection

