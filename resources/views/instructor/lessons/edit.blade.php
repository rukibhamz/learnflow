@extends('layouts.dashboard')

@section('title', 'Edit Lesson')

@section('content')
<div class="max-w-4xl mx-auto">
    @livewire('lesson-editor', ['lesson' => $lesson])
</div>
@endsection
