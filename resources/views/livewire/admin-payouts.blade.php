<div>
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">{{ session('error') }}</div>
    @endif

    {{-- Instructor Balances --}}
    <div class="bg-surface border border-rule rounded-xl overflow-hidden mb-8">
        <table class="w-full">
            <thead>
                <tr class="border-b border-rule text-[11px] font-bold uppercase tracking-widest text-ink3">
                    <th class="text-left px-5 py-3">Instructor</th>
                    <th class="text-right px-5 py-3">Share %</th>
                    <th class="text-right px-5 py-3">Total Due</th>
                    <th class="text-right px-5 py-3">Total Paid</th>
                    <th class="text-right px-5 py-3">Balance</th>
                    <th class="text-right px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($instructorSummaries as $row)
                    <tr class="border-b border-rule last:border-0 hover:bg-bg/50">
                        <td class="px-5 py-3 text-sm font-medium text-ink">{{ $row->instructor->name }}</td>
                        <td class="px-5 py-3 text-sm text-ink3 text-right">{{ $row->share_percent }}%</td>
                        <td class="px-5 py-3 text-sm text-ink text-right">{{ format_price($row->total_due / 100) }}</td>
                        <td class="px-5 py-3 text-sm text-green-600 text-right">{{ format_price($row->total_paid / 100) }}</td>
                        <td class="px-5 py-3 text-sm font-bold {{ $row->balance > 0 ? 'text-amber-600' : 'text-green-600' }} text-right">{{ format_price($row->balance / 100) }}</td>
                        <td class="px-5 py-3 text-right">
                            @if($row->balance > 0)
                                <button wire:click="openCreate({{ $row->instructor->id }})" class="px-3 py-1 text-xs font-bold bg-ink text-white rounded hover:opacity-90">Create Payout</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-sm text-ink3">No instructor earnings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Payout History --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-display font-bold text-base text-ink">Payout History</h3>
        <div class="flex gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search instructor..." class="h-9 px-3 border border-rule rounded-card text-sm w-48">
            <select wire:model.live="statusFilter" class="h-9 px-3 border border-rule rounded-card text-sm bg-surface">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <div class="bg-surface border border-rule rounded-xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-rule text-[11px] font-bold uppercase tracking-widest text-ink3">
                    <th class="text-left px-5 py-3">Instructor</th>
                    <th class="text-left px-5 py-3">Period</th>
                    <th class="text-right px-5 py-3">Amount</th>
                    <th class="text-right px-5 py-3">Platform Fee</th>
                    <th class="text-center px-5 py-3">Status</th>
                    <th class="text-right px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $payout)
                    <tr wire:key="po-{{ $payout->id }}" class="border-b border-rule last:border-0 hover:bg-bg/50">
                        <td class="px-5 py-3 text-sm font-medium text-ink">{{ $payout->instructor?->name ?? 'Deleted' }}</td>
                        <td class="px-5 py-3 text-xs text-ink3">{{ $payout->period_start->format('M j') }} - {{ $payout->period_end->format('M j, Y') }}</td>
                        <td class="px-5 py-3 text-sm text-ink font-bold text-right">{{ $payout->formattedAmount() }}</td>
                        <td class="px-5 py-3 text-xs text-ink3 text-right">{{ $payout->formattedFee() }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded {{ match($payout->status) { 'paid' => 'bg-green-50 text-green-700', 'pending' => 'bg-amber-50 text-amber-700', default => 'bg-gray-100 text-gray-500' } }}">{{ $payout->status }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if($payout->status === 'pending')
                                <button wire:click="markPaid({{ $payout->id }})" wire:confirm="Mark this payout as paid?" class="text-xs text-green-600 font-bold hover:underline mr-2">Pay</button>
                                <button wire:click="cancel({{ $payout->id }})" wire:confirm="Cancel this payout?" class="text-xs text-red-600 font-bold hover:underline">Cancel</button>
                            @elseif($payout->paid_at)
                                <span class="text-xs text-ink3">{{ $payout->paid_at->format('M j, Y') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-sm text-ink3">No payouts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $payouts->links() }}</div>

    {{-- Create Payout Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="$wire.set('showCreateModal', false)">
            <div class="bg-surface rounded-xl w-full max-w-md p-6 border border-rule">
                <h3 class="font-display font-bold text-lg text-ink mb-4">Create Payout</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Instructor</label>
                        <select wire:model="selectedInstructorId" class="w-full h-10 px-3 border border-rule rounded-card text-sm bg-surface">
                            <option value="">Select Instructor</option>
                            @foreach($instructors as $inst)
                                <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Period Start</label>
                            <input type="date" wire:model="periodStart" class="w-full h-10 px-3 border border-rule rounded-card text-sm">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Period End</label>
                            <input type="date" wire:model="periodEnd" class="w-full h-10 px-3 border border-rule rounded-card text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Notes (optional)</label>
                        <input type="text" wire:model="notes" class="w-full h-10 px-3 border border-rule rounded-card text-sm" placeholder="e.g. March 2026 payout">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showCreateModal', false)" class="px-4 py-2 border border-rule rounded-card text-sm text-ink2 hover:bg-bg">Cancel</button>
                    <button wire:click="createPayout" class="px-4 py-2 bg-ink text-white text-sm font-bold rounded-card hover:opacity-90">Create Payout</button>
                </div>
            </div>
        </div>
    @endif
</div>
