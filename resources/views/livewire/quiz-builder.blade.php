<div>
    <div class="bg-surface border border-rule rounded-card p-6 mb-6">
        <h2 class="font-display font-bold text-ink mb-4">Settings</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[12px] font-body text-ink2 mb-1">Time limit (min)</label>
                <input type="number" value="30" class="w-full h-9 px-4 border border-rule rounded-card text-sm">
            </div>
            <div>
                <label class="block text-[12px] font-body text-ink2 mb-1">Attempts</label>
                <input type="number" value="3" class="w-full h-9 px-4 border border-rule rounded-card text-sm">
            </div>
            <div>
                <label class="block text-[12px] font-body text-ink2 mb-1">Passing score %</label>
                <input type="number" value="70" class="w-full h-9 px-4 border border-rule rounded-card text-sm">
            </div>
            <div class="flex items-center gap-2 pt-6">
                <input type="checkbox" id="shuffle" class="rounded border-rule text-accent"> <label for="shuffle" class="text-[13px]">Shuffle</label>
            </div>
        </div>
    </div>
    @foreach($questions as $i => $q)
    <div class="bg-surface border border-rule rounded-card p-6 mb-4">
        <div class="flex items-center gap-2 mb-4">
            <span class="font-display font-bold text-accent">Q{{ $i + 1 }}</span>
            <span class="text-ink3 cursor-grab">⋮⋮</span>
            <button class="ml-auto text-ink3 hover:text-red-600">×</button>
        </div>
        <select class="mb-4 h-9 px-4 border border-rule rounded-card text-sm bg-surface">
            <option>Multiple choice</option>
            <option>True/False</option>
            <option>Short answer</option>
        </select>
        <div class="mb-4">
            <label class="block text-[12px] font-body text-ink2 mb-1">Question</label>
            <textarea rows="2" class="w-full px-4 py-3 border border-rule rounded-card text-sm focus:ring-2 focus:ring-accent">{{ $q['text'] }}</textarea>
        </div>
        <div class="space-y-2">
            @foreach($q['options'] ?? [] as $oi => $opt)
            <div class="flex items-center gap-2">
                <input type="radio" name="q{{ $i }}" {{ $oi == $q['correct'] ? 'checked' : '' }} class="text-accent">
                <input type="text" value="{{ $opt }}" class="flex-1 h-9 px-4 border border-rule rounded-card text-sm">
                <button class="text-ink3 hover:text-red-600">×</button>
            </div>
            @endforeach
            <a href="#" class="text-accent text-[12px] font-body hover:underline">Add option</a>
        </div>
        <div class="mt-4 text-right">
            <input type="number" value="1" class="w-16 h-8 px-2 border border-rule rounded-tag text-sm text-right"> pts
        </div>
    </div>
    @endforeach
    <button class="w-full py-3 border border-dashed border-rule rounded-card text-ink3 font-body text-sm hover:border-ink hover:text-ink transition-colors duration-150">
        Add question
    </button>
</div>
