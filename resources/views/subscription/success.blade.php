@extends('layouts.app')

@section('title', 'Subscription Activated')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center bg-bg">
    <div class="max-w-md mx-auto text-center px-6">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-green-600 text-[32px]">check_circle</span>
        </div>
        <h1 class="font-display font-extrabold text-2xl text-ink mb-3">Subscription Activated!</h1>
        <p class="text-ink2 mb-8">Your subscription is now active. You have access to all included courses.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('courses.index') }}" class="px-6 py-3 bg-ink text-white font-display font-bold text-sm rounded-xl hover:opacity-90 transition-opacity">Browse Courses</a>
            <a href="{{ route('dashboard') }}" class="px-6 py-3 border border-rule text-ink font-display font-bold text-sm rounded-xl hover:bg-bg transition-colors">Go to Dashboard</a>
        </div>
    </div>
</div>
@endsection
