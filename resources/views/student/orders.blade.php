@extends('layouts.dashboard')

@section('title', 'My Orders')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-display font-extrabold text-2xl text-ink">My Orders</h1>
            <p class="text-[13px] font-body text-ink2 mt-1">View your course purchase history and download invoices.</p>
        </div>
    </div>

    <div class="bg-surface border border-rule rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-bg border-b border-rule">
                <tr>
                    <th class="text-left px-5 py-3 font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Course</th>
                    <th class="text-left px-5 py-3 font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Amount</th>
                    <th class="text-left px-5 py-3 font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Date</th>
                    <th class="text-left px-5 py-3 font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Status</th>
                    <th class="text-right px-5 py-3 font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Invoice</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-5 py-3">
                            <div class="flex flex-col">
                                <span class="font-medium text-ink">{{ $order->course->title }}</span>
                                <span class="text-[11px] text-ink3">Order #{{ $order->id }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            {{ strtoupper($order->currency) }} {{ number_format($order->amount, 2) }}
                        </td>
                        <td class="px-5 py-3">
                            {{ $order->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $status = $order->status->value;
                                $map = [
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'paid'    => 'bg-green-50 text-green-700 border-green-200',
                                    'failed'  => 'bg-red-50 text-red-700 border-red-200',
                                ];
                                $cls = $map[$status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest border {{ $cls }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @php $meta = $order->metadata ?? []; @endphp
                            @if(isset($meta['invoice_path']))
                                <a href="{{ route('my-orders.invoice', $order) }}"
                                   class="inline-flex items-center gap-1 text-xs text-primary hover:underline">
                                    <span class="material-symbols-outlined text-[16px]">picture_as_pdf</span>
                                    Download
                                </a>
                            @else
                                <span class="text-[11px] text-ink3">Pending</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-ink3 text-sm">
                            You have no orders yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

