<div>
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div class="flex items-center gap-3 flex-wrap">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink3">search</span>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="h-11 w-72 bg-surface border border-rule rounded-lg pl-10 pr-4 text-sm focus:outline-none focus:border-primary"
                    placeholder="Search coupons...">
            </div>
            <select wire:model.live="statusFilter" class="h-11 bg-surface border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                <option value="">All</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="expired">Expired</option>
            </select>
            @if(count($selected) > 0)
                <button wire:click="bulkDeactivate"
                    class="h-11 px-4 bg-red-500 text-white text-sm font-bold rounded-lg hover:bg-red-600">
                    Deactivate selected ({{ count($selected) }})
                </button>
            @endif
        </div>

        <button wire:click="openCreate"
            class="h-11 px-5 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90">
            + Create Coupon
        </button>
    </div>

    <div class="bg-surface border border-rule rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-bg border-b border-rule">
                <tr>
                    <th class="px-5 py-3 text-left">
                        <input type="checkbox" wire:model="selectAllOnPage" class="rounded border-rule">
                    </th>
                    <th class="px-5 py-3 text-left font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Code</th>
                    <th class="px-5 py-3 text-left font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Type</th>
                    <th class="px-5 py-3 text-left font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Amount</th>
                    <th class="px-5 py-3 text-left font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Used</th>
                    <th class="px-5 py-3 text-left font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Expiry</th>
                    <th class="px-5 py-3 text-left font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Status</th>
                    <th class="px-5 py-3 text-right font-display font-bold text-[11px] uppercase tracking-widest text-ink3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule">
                @forelse($coupons as $coupon)
                    <tr class="hover:bg-bg">
                        <td class="px-5 py-3">
                            <input type="checkbox" value="{{ $coupon->id }}" wire:model="selected" class="rounded border-rule">
                        </td>
                        <td class="px-5 py-3">
                            <div class="font-bold text-ink">{{ $coupon->code }}</div>
                            <div class="text-[11px] text-ink3">{{ $coupon->name }}</div>
                        </td>
                        <td class="px-5 py-3 text-ink2">
                            {{ ucfirst($coupon->discount_type->value) }}
                        </td>
                        <td class="px-5 py-3 text-ink2">
                            @if($coupon->discount_type->value === 'fixed')
                                ${{ number_format((float) $coupon->amount, 2) }}
                            @else
                                {{ number_format((float) $coupon->amount, 0) }}%
                            @endif
                        </td>
                        <td class="px-5 py-3 text-ink2">
                            {{ $coupon->used_count }}@if($coupon->max_uses) / {{ $coupon->max_uses }}@endif
                        </td>
                        <td class="px-5 py-3 text-ink2">
                            @if($coupon->expires_at)
                                {{ $coupon->expires_at->format('M j, Y') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <button wire:click="toggleActive({{ $coupon->id }})"
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest border
                                {{ $coupon->is_active ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right space-x-2">
                            <button wire:click="openUsage({{ $coupon->id }})" class="text-xs text-primary hover:underline">Usage</button>
                            @if(!$coupon->stripe_coupon_id)
                                <button wire:click="createStripeCoupon({{ $coupon->id }})" class="text-xs text-ink3 hover:underline">Create Stripe</button>
                            @else
                                <span class="text-[11px] text-ink3">Stripe ✓</span>
                            @endif
                            <button wire:click="openEdit({{ $coupon->id }})" class="text-xs text-ink hover:underline">Edit</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-ink3">No coupons found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($coupons->hasPages())
        <div class="mt-6">{{ $coupons->links() }}</div>
    @endif

    {{-- Create/Edit Modal --}}
    @if($showFormModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="$set('showFormModal', false)">
            <div class="bg-surface rounded-xl shadow-xl w-full max-w-xl p-6">
                <h3 class="font-display font-bold text-lg text-ink mb-6">
                    {{ $editingId ? 'Edit Coupon' : 'Create Coupon' }}
                </h3>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Code *</label>
                            <input wire:model="code" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" placeholder="SAVE20">
                            @error('code') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Name *</label>
                            <input wire:model="name" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" placeholder="Launch promo">
                            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Discount Type *</label>
                            <select wire:model="discount_type" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Amount *</label>
                            <input type="number" step="0.01" wire:model="amount" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                            @error('amount') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Min Amount</label>
                            <input type="number" step="0.01" wire:model="minimum_amount" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Max Uses</label>
                            <input type="number" wire:model="max_uses" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Expires At</label>
                            <input type="date" wire:model="expires_at" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 pt-2">
                            <input type="checkbox" wire:model="is_active" class="rounded border-rule">
                            <span class="text-sm text-ink2">Active</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Stripe Coupon ID</label>
                            <input wire:model="stripe_coupon_id" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" placeholder="coupon_...">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" wire:click="$set('showFormModal', false)" class="px-5 py-2.5 border border-rule rounded-lg text-sm text-ink2 hover:bg-bg">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 bg-ink text-white rounded-lg text-sm font-bold hover:opacity-90">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Usage Modal --}}
    @if($showUsageModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="closeUsage">
            <div class="bg-surface rounded-xl shadow-xl w-full max-w-2xl p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-display font-bold text-lg text-ink">Coupon Usage</h3>
                        @if($usageCoupon)
                            <p class="text-xs text-ink3 mt-1">{{ $usageCoupon->code }} — {{ $usageCoupon->name }}</p>
                        @endif
                    </div>
                    <button type="button" wire:click="closeUsage" class="p-2 hover:bg-bg rounded-lg">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="bg-bg border border-rule rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="border-b border-rule">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] uppercase tracking-widest text-ink3">User</th>
                                <th class="px-4 py-3 text-left text-[11px] uppercase tracking-widest text-ink3">Order</th>
                                <th class="px-4 py-3 text-left text-[11px] uppercase tracking-widest text-ink3">Used At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rule">
                            @forelse($usage as $row)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-ink">{{ $row->user?->name }}</div>
                                        <div class="text-[11px] text-ink3">{{ $row->user?->email }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-ink2">#{{ $row->order_id }}</td>
                                    <td class="px-4 py-3 text-ink2">{{ $row->used_at?->format('M j, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-ink3">No usage yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

