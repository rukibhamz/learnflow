<button wire:click="toggle" 
    class="p-2 rounded-lg border transition-colors {{ $isInWishlist ? 'bg-red-50 border-red-200 text-red-500' : 'bg-bg border-rule text-ink3 hover:text-red-500 hover:border-red-200' }}"
    title="{{ $isInWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}">
    <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' {{ $isInWishlist ? '1' : '0' }}">bookmark</span>
</button>
