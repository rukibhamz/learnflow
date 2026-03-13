@extends('layouts.dashboard')

@section('title', $id ? 'Edit Course' : 'New Course')
@section('sidebar_nav')
    <a href="http://localhost/learnflow/dashboard" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">Dashboard</a>
    <a href="http://localhost/learnflow/instructor/courses" class="block py-2 px-3 text-[13px] rounded-card border-r-2 border-accent pr-2 bg-accent-bg text-accent font-medium">My Courses</a>
    <a href="http://localhost/learnflow/instructor/earnings" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">Earnings</a>
@endsection

@section('content')
@livewire('course-form', ['courseId' => $id])
@endsection
