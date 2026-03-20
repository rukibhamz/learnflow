@extends('layouts.admin')

@section('title', 'Course Curriculum - ' . $course->title)

@section('content')
<div class="max-w-4xl mx-auto">
    @livewire('course-curriculum', ['course' => $course, 'fromAdmin' => true])
</div>
@endsection
