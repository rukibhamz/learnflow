@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
    <livewire:admin-category-form :category="$category" />
@endsection
