@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-10">
        <h1 class="font-display font-extrabold text-2xl text-ink">Welcome back, {{ explode(' ', auth()->user()->name ?? 'Learner')[0] }}.</h1>
        <p class="text-[13px] font-body text-ink2 mt-1">You're logged in! Explore your courses and progress.</p>
    </div>
    
    {{-- Re-include student dashboard metrics if available --}}
    @livewire('student-dashboard')
</div>
@endsection
