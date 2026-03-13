@props(['percentage', 'color' => '#1A43E0', 'animate' => false])

<div class="w-full h-[3px] bg-rule rounded-full overflow-hidden">
    <div class="h-full transition-all duration-500 {{ $animate ? 'animate-progress' : '' }}" 
         style="width: {{ $percentage }}%; background-color: {{ $color }};">
    </div>
</div>

@if($animate)
<style>
    @keyframes progress-grow {
        from { width: 0; }
        to { width: {{ $percentage }}%; }
    }
    .animate-progress {
        animation: progress-grow 1s ease-out forwards;
    }
</style>
@endif
