@extends('layouts.admin')

@section('title', 'Course Review')
@section('content')
<h1 class="font-display font-extrabold text-xl text-ink mb-6">Course Review Queue</h1>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach([
        ['title' => 'Web Development Bootcamp', 'instructor' => 'Jane Doe', 'date' => 'Mar 10, 2025', 'lessons' => 24, 'price' => 49],
        ['title' => 'Data Science Intro', 'instructor' => 'John Smith', 'date' => 'Mar 9, 2025', 'lessons' => 18, 'price' => 79],
    ] as $course)
    <div class="bg-surface border border-rule rounded-card overflow-hidden">
        <div class="h-20 bg-accent-bg flex items-center justify-center font-display font-extrabold text-accent text-lg">
            {{ strtoupper(substr($course['title'], 0, 2)) }}
        </div>
        <div class="p-4">
            <h3 class="font-display font-bold text-ink">{{ $course['title'] }}</h3>
            <p class="text-[12px] text-ink3 mt-1">{{ $course['instructor'] }} • {{ $course['date'] }}</p>
            <p class="text-[12px] text-ink3">{{ $course['lessons'] }} lessons • ${{ $course['price'] }}</p>
        </div>
        <div class="p-4 flex gap-3 border-t border-rule">
            <button class="flex-1 py-2 bg-success-bg text-success font-display font-bold text-sm rounded-card hover:opacity-90 transition-opacity duration-150">Approve</button>
            <button class="flex-1 py-2 border border-rule text-warn font-body text-sm rounded-card hover:border-warn transition-colors duration-150">Reject</button>
        </div>
    </div>
    @endforeach
</div>
@if(false)
<div class="text-center py-16 text-ink3 font-body">No courses pending review</div>
@endif
@endsection
