@extends('layouts.dashboard')

@section('title', 'Edit Lesson')
@section('sidebar_nav')
    <a href="http://localhost/learnflow/instructor/courses" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">My Courses</a>
@endsection

@section('content')
@livewire('lesson-editor', ['lessonId' => $id])
@endsection
