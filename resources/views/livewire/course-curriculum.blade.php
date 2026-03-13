<div class="flex justify-between items-center mb-6">
    <h1 class="font-display font-extrabold text-xl text-ink">Curriculum</h1>
    <button class="px-5 py-2.5 bg-ink text-white font-display font-bold text-sm rounded-card">Add section</button>
</div>
<div class="space-y-4">
    @foreach($sections as $section)
    <div class="bg-surface border border-rule rounded-card overflow-hidden">
        <div class="flex items-center gap-3 p-4">
            <span class="text-ink3 cursor-grab">⋮⋮</span>
            <h3 class="font-display font-bold text-ink flex-1">{{ $section['title'] }}</h3>
            <span class="text-[12px] text-ink3">{{ count($section['lessons']) }} lessons</span>
            <a href="#" class="text-accent text-[12px] font-body hover:underline">Add lesson</a>
            <a href="#" class="text-ink3 text-[12px] hover:text-red-600">Delete</a>
        </div>
        <div class="border-t border-rule pl-8 ml-3 border-l border-rule">
            @foreach($section['lessons'] as $lesson)
            <div class="flex items-center gap-3 py-3 px-4 border-b border-rule last:border-0">
                <span class="text-ink3 cursor-grab">⋮⋮</span>
                <span class="w-8 h-8 rounded-tag flex items-center justify-center text-xs {{ $lesson['type'] === 'video' ? 'bg-accent-bg text-accent' : 'bg-bg text-ink3' }}">
                    {{ $lesson['type'] === 'video' ? '▶' : 'T' }}
                </span>
                <span class="flex-1 font-body text-[13px]">{{ $lesson['title'] }}</span>
                <span class="text-[12px] text-ink3">{{ $lesson['duration'] }}</span>
                <a href="#" class="text-accent text-[12px] hover:underline">Edit</a>
                <button class="text-ink3 hover:text-red-600">×</button>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
