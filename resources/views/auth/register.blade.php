@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center p-6">
    <div class="mb-8 text-center">
        <h2 class="font-display font-extrabold text-2xl text-ink uppercase tracking-tight">Learn<span class="text-accent">Flow</span></h2>
    </div>

    <div class="w-full max-w-[400px] bg-surface border border-rule rounded-card p-8">
        <h1 class="font-display font-bold text-xl text-ink mb-8">Create your account</h1>
        
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2 mb-2">Full Name</label>
                <input type="text" name="name" required autofocus class="w-full h-9 bg-bg border border-rule rounded-card px-3 font-body text-sm focus:outline-none focus:border-accent transition-colors">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2 mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full h-9 bg-bg border border-rule rounded-card px-3 font-body text-sm focus:outline-none focus:border-accent transition-colors">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2 mb-2">Password</label>
                <input type="password" name="password" required class="w-full h-9 bg-bg border border-rule rounded-card px-3 font-body text-sm focus:outline-none focus:border-accent transition-colors">
            </div>

            <button type="submit" class="w-full h-9 bg-ink text-white font-display font-bold text-[13px] rounded-card hover:opacity-90 transition-opacity mt-6">Get started</button>
        </form>

        <p class="mt-8 text-center text-[12px] text-ink2 font-body">
            Already have an account? <a href="{{ route('login') }}" class="text-accent font-bold hover:underline">Log in</a>
        </p>
    </div>
</div>
@endsection
