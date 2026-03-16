@extends('layouts.admin')

@section('title', 'Course Review Queue')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="font-poppins font-bold text-lg tracking-tight text-ink">Course Review Queue</h1>
        <p class="text-[13px] text-ink2 font-body mt-1">Review and approve courses submitted by instructors.</p>
    </div>

    @livewire('admin-course-review')
</div>
@endsection
