@extends('layouts.dashboard')

@section('title', 'Course Curriculum')

@section('content')
<div class="max-w-4xl mx-auto">
    @livewire('course-curriculum', ['course' => $course])
</div>
@endsection
