@extends('layouts.dashboard')

@section('title', 'My Courses')
@section('sidebar_nav')
    <a href="http://localhost/learnflow/dashboard" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">Dashboard</a>
    <a href="http://localhost/learnflow/instructor/courses" class="block py-2 px-3 text-[13px] rounded-card border-r-2 border-accent pr-2 bg-accent-bg text-accent font-medium">My Courses</a>
    <a href="http://localhost/learnflow/instructor/earnings" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">Earnings</a>
    <a href="#" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">Quiz Builder</a>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="font-display font-extrabold text-xl text-ink">My Courses</h1>
    <a href="http://localhost/learnflow/instructor/courses/new" class="px-5 py-2.5 bg-ink text-white font-display font-bold text-sm rounded-card hover:opacity-90 transition-opacity duration-150">New course</a>
</div>
@livewire('instructor-course-index')
@endsection
