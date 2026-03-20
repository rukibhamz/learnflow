@extends('layouts.dashboard')

@section('title', 'Settings')

@section('content')
<div class="max-w-5xl mx-auto" x-data="{ tab: 'profile' }">
    <div class="mb-10">
        <h1 class="font-display font-extrabold text-2xl text-ink">Account Settings</h1>
        <p class="text-[13px] font-body text-ink2 mt-1">Manage your profile and account preferences.</p>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-8 border-b border-rule mb-10">
        <button @click="tab = 'profile'" :class="tab === 'profile' ? 'border-accent text-ink' : 'border-transparent text-ink3 hover:text-ink2'" class="pb-4 font-display font-bold text-[13px] border-b-2 transition-colors">Profile</button>
        <button @click="tab = 'password'" :class="tab === 'password' ? 'border-accent text-ink' : 'border-transparent text-ink3 hover:text-ink2'" class="pb-4 font-display font-bold text-[13px] border-b-2 transition-colors">Password</button>
        <button @click="tab = 'notifications'" :class="tab === 'notifications' ? 'border-accent text-ink' : 'border-transparent text-ink3 hover:text-ink2'" class="pb-4 font-display font-bold text-[13px] border-b-2 transition-colors">Notifications</button>
        <button @click="tab = 'billing'" :class="tab === 'billing' ? 'border-accent text-ink' : 'border-transparent text-ink3 hover:text-ink2'" class="pb-4 font-display font-bold text-[13px] border-b-2 transition-colors">Billing</button>
    </div>

    {{-- Profile Tab --}}
    <div x-show="tab === 'profile'">
        <div class="bg-surface border border-rule rounded-card p-8">
            <div class="max-w-xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>
    </div>

    {{-- Password Tab --}}
    <div x-show="tab === 'password'" x-cloak>
        <div class="bg-surface border border-rule rounded-card p-8">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </div>
    </div>

    {{-- Notifications Tab --}}
    <div x-show="tab === 'notifications'" x-cloak>
        <div class="bg-surface border border-rule rounded-card p-8">
            <div class="max-w-xl">
                <livewire:notification-preferences />
            </div>
        </div>
    </div>

    {{-- Billing Tab --}}
    <div x-show="tab === 'billing'" x-cloak>
        <div class="bg-surface border border-rule rounded-card p-8">
            @php
                $orders = \App\Models\Order::where('user_id', auth()->id())
                    ->with('course')
                    ->latest()
                    ->take(10)
                    ->get();
            @endphp

            <h3 class="font-display font-bold text-lg text-ink mb-1">Purchase History</h3>
            <p class="text-[13px] font-body text-ink2 mb-6">Your recent course purchases.</p>

            @if($orders->isNotEmpty())
                <div class="bg-bg rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-rule">
                                <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ink3">Course</th>
                                <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ink3">Amount</th>
                                <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ink3">Status</th>
                                <th class="text-left px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ink3">Date</th>
                                <th class="text-right px-5 py-3 text-[10px] font-bold uppercase tracking-widest text-ink3">Invoice</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule">
                            @foreach($orders as $order)
                            <tr>
                                <td class="px-5 py-3 text-ink font-medium">{{ $order->course?->title ?? '—' }}</td>
                                <td class="px-5 py-3 text-ink2">{{ format_price($order->amount) }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $colors = ['paid' => 'text-green-700 bg-green-50', 'pending' => 'text-amber-700 bg-amber-50', 'refunded' => 'text-blue-700 bg-blue-50', 'failed' => 'text-red-700 bg-red-50'];
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $colors[$order->status->value] ?? 'text-ink3 bg-bg' }}">
                                        {{ $order->status->value }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-ink3 text-xs">{{ $order->created_at->format('M j, Y') }}</td>
                                <td class="px-5 py-3 text-right">
                                    @if($order->status->value === 'paid' && ($order->metadata['invoice_path'] ?? null))
                                        <a href="{{ route('my-orders.invoice', $order) }}" class="text-xs text-primary hover:underline">Download</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($orders->count() >= 10)
                    <div class="mt-4 text-center">
                        <a href="{{ route('my-orders') }}" class="text-sm text-primary hover:underline">View all orders →</a>
                    </div>
                @endif
            @else
                <div class="text-center py-8 text-ink3">
                    <span class="material-symbols-outlined text-[36px] mb-2 block">receipt_long</span>
                    <p class="text-sm">No purchases yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
