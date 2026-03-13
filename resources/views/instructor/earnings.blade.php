@extends('layouts.dashboard')

@section('title', 'Earnings')
@section('sidebar_nav')
    <a href="http://localhost/learnflow/dashboard" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">Dashboard</a>
    <a href="http://localhost/learnflow/instructor/courses" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">My Courses</a>
    <a href="http://localhost/learnflow/instructor/earnings" class="block py-2 px-3 text-[13px] rounded-card border-r-2 border-accent pr-2 bg-accent-bg text-accent font-medium">Earnings</a>
    <a href="#" class="block py-2 px-3 text-[13px] rounded-card text-ink2 hover:bg-bg hover:text-ink">Quiz Builder</a>
@endsection

@section('content')
<h1 class="font-display font-extrabold text-xl text-ink mb-6">Earnings</h1>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-surface border border-rule rounded-card p-6">
        <p class="text-[11px] font-body text-ink3 mb-1">Total revenue</p>
        <p class="font-display font-extrabold text-2xl text-ink">$12,450</p>
    </div>
    <div class="bg-surface border border-rule rounded-card p-6">
        <p class="text-[11px] font-body text-ink3 mb-1">This month</p>
        <p class="font-display font-extrabold text-2xl text-ink">$1,240</p>
        <p class="text-[11px] text-success mt-1">+12%</p>
    </div>
    <div class="bg-surface border border-rule rounded-card p-6">
        <p class="text-[11px] font-body text-ink3 mb-1">Pending payout</p>
        <p class="font-display font-extrabold text-2xl text-ink">$320</p>
    </div>
</div>
<div class="bg-surface border border-rule rounded-card p-8 h-80 flex items-center justify-center text-ink3 font-body text-sm">
    Chart — integrate Chart.js
</div>
<div class="mt-8 bg-surface border border-rule rounded-card overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-rule">
                <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase text-ink3">Date</th>
                <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase text-ink3">Amount</th>
                <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase text-ink3">Status</th>
                <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase text-ink3">Bank</th>
            </tr>
        </thead>
        <tbody>
            @foreach([['date' => 'Mar 10, 2025', 'amount' => '$450', 'status' => 'paid'], ['date' => 'Feb 28, 2025', 'amount' => '$320', 'status' => 'paid'], ['date' => 'Feb 15, 2025', 'amount' => '$280', 'status' => 'pending']] as $row)
            <tr class="border-b border-rule">
                <td class="py-3 px-4 font-body text-[13px]">{{ $row['date'] }}</td>
                <td class="py-3 px-4 font-body text-[13px]">{{ $row['amount'] }}</td>
                <td class="py-3 px-4">@include('components.status-badge', ['status' => $row['status']])</td>
                <td class="py-3 px-4 font-body text-[13px] text-ink3">•••• 4521</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
