<div x-data="{ open: false }" class="relative">
    <button
        type="button"
        @click="open = !open"
        class="p-2 text-ink2 hover:text-ink transition-colors duration-150 rounded-card focus:outline-none focus-visible:ring-2 focus-visible:ring-accent"
        aria-label="Notifications"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-accent text-white font-display font-bold text-[11px] rounded-full">{{ $unreadCount }}</span>
        @endif
    </button>
    <div
        x-show="open"
        x-cloak
        x-transition
        @click.outside="open = false"
        class="absolute right-0 top-full mt-2 w-[280px] bg-surface border border-rule rounded-card z-50 overflow-hidden"
    >
        <div class="p-3 border-b border-rule">
            <button wire:click="markAllRead" class="text-accent text-[12px] font-body hover:underline">Mark all read</button>
        </div>
        <div class="max-h-[320px] overflow-y-auto">
            @foreach([
                ['text' => 'New lesson added to Web Development', 'unread' => true],
                ['text' => 'Your certificate is ready', 'unread' => true],
                ['text' => 'Course Data Science updated', 'unread' => false],
                ['text' => 'New review on your course', 'unread' => false],
                ['text' => 'Payout processed', 'unread' => false],
            ] as $n)
                <div class="px-4 py-3 h-11 flex items-center text-[13px] font-body {{ $n['unread'] ? 'border-l-[3px] border-accent bg-accent-bg' : '' }}">
                    {{ $n['text'] }}
                </div>
            @endforeach
        </div>
    </div>
</div>
