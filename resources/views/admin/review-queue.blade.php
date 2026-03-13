@extends('layouts.admin')

@section('title', 'Review Queue')

@section('content')
<div class="space-y-10">
    <div class="mb-4">
        <p class="text-[13px] font-body text-ink2">Total 12 courses pending review. Priority items marked in yellow.</p>
    </div>

    <div class="grid grid-cols-1 gap-4">
        @foreach([
            ['title' => 'Advanced Rust Systems', 'instructor' => 'Jane Rust', 'submitted' => '2 hours ago', 'priority' => true],
            ['title' => 'Machine Learning 101', 'instructor' => 'Dr. Smith', 'submitted' => '5 hours ago', 'priority' => false],
            ['title' => 'The Art of Cooking', 'instructor' => 'Chef Gordon', 'submitted' => 'Yesterday', 'priority' => false],
        ] as $c)
        <div class="bg-surface border {{ $c['priority'] ? 'border-warn' : 'border-rule' }} rounded-card p-6 flex items-center justify-between group transition-all hover:bg-bg">
            <div class="flex items-center gap-6">
                <div class="w-12 h-12 bg-accent-bg flex items-center justify-center font-display font-extrabold text-accent text-lg rounded-card">
                    {{ strtoupper(substr($c['title'], 0, 1)) }}
                </div>
                <div>
                    <h3 class="font-display font-bold text-sm text-ink group-hover:text-accent transition-colors leading-tight mb-1">{{ $c['title'] }}</h3>
                    <p class="text-[11px] font-body text-ink3">Instructor: <span class="text-ink2 font-medium">{{ $c['instructor'] }}</span> • Submitted {{ $c['submitted'] }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="px-5 py-2 border border-rule rounded-card font-display font-bold text-[11px] uppercase tracking-widest text-ink hover:border-ink transition-colors">Preview</button>
                <div class="flex">
                    <button class="px-5 py-2 bg-success text-white font-display font-bold text-[11px] uppercase tracking-widest rounded-l-card hover:opacity-90 transition-opacity">Approve</button>
                    <button class="px-5 py-2 bg-warn text-white font-display font-bold text-[11px] uppercase tracking-widest rounded-r-card hover:opacity-90 transition-opacity">Reject</button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
