@props(['status'])

@php
    $styles = [
        'draft' => 'bg-rule text-ink3',
        'review' => 'bg-warn-bg text-warn',
        'published' => 'bg-success-bg text-success',
        'archived' => 'bg-rule text-ink3',
        'free' => 'bg-success-bg text-success',
        'paid' => 'bg-accent-bg text-accent',
        'pending' => 'bg-warn-bg text-warn',
    ];
    
    $currentStyle = $styles[strtolower($status)] ?? 'bg-rule text-ink2';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded-pill text-[10px] font-bold uppercase tracking-wider font-display $currentStyle"]) }}>
    {{ $status }}
</span>
