<div class="space-y-3">
    <div class="flex items-center justify-between">
        <p class="text-[11px] font-bold uppercase tracking-widest text-ink3">Coupon</p>
        @if(($result['valid'] ?? false))
            <button type="button" wire:click="remove" class="text-[11px] font-bold text-primary uppercase tracking-widest hover:opacity-80">
                Remove
            </button>
        @endif
    </div>

    <div class="flex gap-2">
        <input
            type="text"
            wire:model.defer="code"
            placeholder="Enter code"
            class="flex-1 h-10 bg-bg border border-rule rounded-lg px-3 text-sm focus:outline-none focus:border-primary"
            @disabled(($result['valid'] ?? false))
        />
        <button
            type="button"
            wire:click="apply"
            class="h-10 px-4 bg-ink text-white font-display font-bold text-xs rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50"
            @disabled(($result['valid'] ?? false))
        >
            Apply
        </button>
    </div>

    @error('code')
        <p class="text-xs text-red-600">{{ $message }}</p>
    @enderror

    @if($result)
        @if($result['valid'])
            <div class="p-3 bg-green-50 border border-green-200 rounded-lg text-xs text-green-800">
                <div class="flex items-center justify-between gap-3">
                    <span class="font-medium">
                        {{ strtoupper($result['code']) }} applied
                    </span>
                    <span class="font-bold">
                        -{{ format_price($result['discount']) }}
                    </span>
                </div>
            </div>
        @else
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800">
                {{ $result['message'] }}
            </div>
        @endif
    @endif
</div>

