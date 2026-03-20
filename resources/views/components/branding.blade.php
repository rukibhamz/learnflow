@props(['class' => '', 'href' => null, 'showIcon' => true, 'variant' => 'default'])

@php
    $href = $href ?? route('home');
    $brandColor = $siteColor ?? '#1a42e0';
    $textClass = ($variant === 'dark') ? 'text-white' : 'text-ink';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex items-center gap-2 group ' . $class]) }}>
    @if($siteLogoUrl ?? null)
        <img src="{{ $siteLogoUrl }}" alt="{{ $siteName }}" class="h-8 md:h-10 object-contain object-left max-w-[180px]">
    @else
        @if($showIcon)
            <x-icon name="school" class="w-8 h-8 shrink-0" style="color: {{ $brandColor }}" />
        @endif
        @if($variant === 'split' || $variant === 'dark')
            @php $parts = explode(' ', $siteName ?? 'LearnFlow'); $first = array_shift($parts); $rest = implode(' ', $parts); @endphp
            <span class="text-xl font-bold tracking-tight font-display {{ $textClass }}">{{ $first }}</span>
            @if($rest)
                <span class="text-xl font-bold tracking-tight font-display" style="color: {{ $brandColor }}">{{ $rest }}</span>
            @endif
        @else
            <span class="text-xl font-bold tracking-tight font-display {{ $textClass }}">{{ $siteName ?? 'LearnFlow' }}</span>
        @endif
    @endif
</a>
