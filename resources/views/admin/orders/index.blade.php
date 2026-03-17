@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="font-display font-extrabold text-2xl text-ink">Orders & Refunds</h1>
        <p class="text-[13px] font-body text-ink2 mt-1">Manage payments and process refunds.</p>
    </div>

    @livewire('admin-order-table')
</div>
@endsection
