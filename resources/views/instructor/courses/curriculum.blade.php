@extends('layouts.dashboard')

@section('title', 'Curriculum')
@section('sidebar_nav')
    <a href="http://localhost/learnflow/instructor/courses" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">My Courses</a>
    <a href="http://localhost/learnflow/instructor/courses/{{ $id }}/curriculum" class="block py-2 px-3 text-[13px] rounded-card border-r-2 border-accent pr-2 bg-accent-bg text-accent font-medium">Curriculum</a>
@endsection

@section('content')
@livewire('course-curriculum', ['courseId' => $id])
@endsection
