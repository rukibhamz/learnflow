<div>
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-4 mb-6">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by student, email, or course..."
            class="flex-1 min-w-[200px] h-10 px-4 border border-rule rounded-card text-sm focus:outline-none focus:border-primary">
        <select wire:model.live="statusFilter" class="h-10 px-4 border border-rule rounded-card text-sm bg-surface">
            <option value="">All Statuses</option>
            <option value="paid">Paid</option>
            <option value="pending">Pending</option>
            <option value="refunded">Refunded</option>
            <option value="failed">Failed</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-surface border border-rule rounded-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-bg border-b border-rule">
                <tr>
                    <th class="text-left px-5 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Order</th>
                    <th class="text-left px-5 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Student</th>
                    <th class="text-left px-5 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Course</th>
                    <th class="text-left px-5 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Amount</th>
                    <th class="text-left px-5 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Status</th>
                    <th class="text-left px-5 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Date</th>
                    <th class="text-right px-5 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-5 py-3 font-mono text-xs text-ink3">#{{ $order->id }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-ink">{{ $order->user?->name }}</div>
                            <div class="text-[11px] text-ink3">{{ $order->user?->email }}</div>
                        </td>
                        <td class="px-5 py-3 text-ink2">{{ $order->course?->title }}</td>
                        <td class="px-5 py-3 font-medium text-ink">${{ number_format($order->amount, 2) }}</td>
                        <td class="px-5 py-3">
                            @php
                                $colors = ['paid' => 'bg-green-50 text-green-700', 'pending' => 'bg-amber-50 text-amber-700', 'refunded' => 'bg-blue-50 text-blue-700', 'failed' => 'bg-red-50 text-red-700'];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase {{ $colors[$order->status->value] ?? 'bg-gray-50 text-gray-700' }}">
                                {{ $order->status->value }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-ink3 text-xs">{{ $order->created_at->format('M j, Y') }}</td>
                        <td class="px-5 py-3 text-right">
                            @if($order->status->value === 'paid')
                                <button wire:click="openRefundModal({{ $order->id }})" class="text-xs text-red-600 hover:underline font-medium">
                                    Refund
                                </button>
                            @elseif($order->status->value === 'refunded')
                                <span class="text-xs text-ink3">Refunded</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-ink3">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>

    {{-- Refund Modal --}}
    @if($refundingOrderId)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="$wire.closeRefundModal()">
            <div class="bg-surface rounded-xl w-full max-w-md p-6 border border-rule">
                <h3 class="font-display font-bold text-lg text-ink mb-4">Process Refund</h3>
                <p class="text-sm text-ink2 mb-4">This will refund the payment via Stripe and remove the student's enrollment.</p>

                <div class="mb-4">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Reason (optional)</label>
                    <textarea wire:model="refundReason" rows="2" class="w-full px-4 py-2 border border-rule rounded-card text-sm focus:outline-none focus:border-primary resize-none" placeholder="Reason for refund..."></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="closeRefundModal" class="px-4 py-2 border border-rule rounded-card text-sm text-ink2 hover:bg-bg">Cancel</button>
                    <button wire:click="processRefund" class="px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-card hover:bg-red-700">Process Refund</button>
                </div>
            </div>
        </div>
    @endif
</div>
