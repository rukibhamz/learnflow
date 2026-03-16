@extends('layouts.admin')

@section('title', 'Coupons')

@section('content')
<div class="space-y-1 mb-10">
    <h1 class="font-poppins font-bold text-lg tracking-tight text-ink">Coupons</h1>
    <p class="text-[13px] font-body text-ink2">Create and manage discount codes.</p>
</div>

@livewire('admin-coupon-table')
@endsection

