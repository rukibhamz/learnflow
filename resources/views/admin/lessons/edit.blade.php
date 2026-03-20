@extends('layouts.admin')

@section('title', 'Edit Lesson - ' . $lesson->title)

@section('content')
<div class="max-w-4xl mx-auto">
    @livewire('lesson-editor', ['lesson' => $lesson, 'fromAdmin' => true])
</div>
@endsection
