@extends('layouts.app')

@section('title', 'Forgot Password')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center p-6">
    <div class="w-full max-w-[400px] bg-surface border border-rule rounded-card p-8">
        <h1 class="font-display font-bold text-xl text-ink mb-4">Reset Password</h1>
        <p class="text-[13px] text-ink2 font-body mb-8 leading-relaxed">Enter your email and we'll send you a link to reset your password.</p>
        
        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2 mb-2">Email Address</label>
                <input type="email" name="email" :value="old('email')" required autofocus class="w-full h-9 bg-bg border border-rule rounded-card px-3 font-body text-sm focus:outline-none focus:border-accent transition-colors">
            </div>

            <button type="submit" class="w-full h-9 bg-ink text-white font-display font-bold text-[13px] rounded-card hover:opacity-90 transition-opacity mt-4">Send Link</button>
        </form>

        <p class="mt-8 text-center text-[12px] text-ink2 font-body">
            <a href="{{ route('login') }}" class="text-ink font-bold hover:underline">← Back to login</a>
        </p>
    </div>
</div>
@endsection
