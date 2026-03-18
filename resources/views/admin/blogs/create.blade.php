@extends('layouts.admin')

@section('title', 'Create Blog Post')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <script src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <style>
        .trix-editor {
            min-height: 700px !important;
        }
        trix-toolbar .trix-button-group--file-tools { display: none !important; }
    </style>
@endpush

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold font-display text-ink">Create Blog Post</h1>
    </div>

    @livewire('admin-blog-form')
@endsection
