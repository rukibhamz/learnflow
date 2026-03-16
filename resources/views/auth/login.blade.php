@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center p-6">
    <div class="mb-8 text-center">
        <h2 class="font-display font-extrabold text-2xl text-ink uppercase tracking-tight">Learn<span class="text-accent">Flow</span></h2>
    </div>

    <div class="w-full max-w-[400px] bg-surface border border-rule rounded-card p-8">
        <h1 class="font-display font-bold text-xl text-ink mb-8">Welcome back</h1>
        
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-widest text-ink2 mb-2">Email or Username</label>
                <input type="text" name="login" value="{{ old('login') }}" required autofocus class="w-full h-9 bg-bg border border-rule rounded-card px-3 font-body text-sm focus:outline-none focus:border-accent transition-colors @error('login') border-red-400 @enderror">
                @error('login')
                    <p class="mt-1 text-[11px] text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex justify-between mb-2">
                    <label class="text-[11px] font-bold uppercase tracking-widest text-ink2">Password</label>
                    <a href="{{ route('password.request') }}" class="text-[11px] font-bold uppercase tracking-widest text-accent hover:underline">Forgot?</a>
                </div>
                <input type="password" name="password" required class="w-full h-9 bg-bg border border-rule rounded-card px-3 font-body text-sm focus:outline-none focus:border-accent transition-colors">
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="rounded border-rule text-accent focus:ring-accent">
                <label for="remember" class="text-[12px] text-ink2">Remember me</label>
            </div>

            <button type="submit" class="w-full h-9 bg-ink text-white font-display font-bold text-[13px] rounded-card hover:opacity-90 transition-opacity mt-4">Log in</button>
        </form>

        <div class="relative my-8 text-center">
            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-rule"></div></div>
            <span class="relative px-3 bg-surface text-[10px] font-bold uppercase tracking-[0.2em] text-ink3">Or continue with</span>
        </div>

        <button class="w-full h-9 border border-rule rounded-card flex items-center justify-center gap-3 font-body text-sm font-medium hover:bg-bg transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24"><path fill="#EA4335" d="M12.48 10.92v3.28h7.84c-.24 1.84-2.24 5.36-7.84 5.36-4.8 0-8.72-3.92-8.72-8.72s3.92-8.72 8.72-8.72c2.72 0 4.56 1.12 5.6 2.08l2.56-2.48C19.12 1.92 16.08 0 12.48 0 5.6 0 0 5.6 0 12.48s5.6 12.48 12.48 12.48c7.2 0 12-5.04 12-12.24 0-.8-.08-1.44-.24-2.32h-11.76z"/></svg>
            Google
        </button>

        <p class="mt-8 text-center text-[12px] text-ink2 font-body">
            New here? <a href="{{ route('register') }}" class="text-accent font-bold hover:underline">Create account</a>
        </p>
    </div>
</div>
@endsection
