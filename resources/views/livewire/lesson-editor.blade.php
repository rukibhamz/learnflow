<div class="bg-surface border border-rule rounded-card p-6 max-w-2xl">
    <div class="space-y-4">
        <div>
            <label class="block text-[12px] font-body font-medium text-ink2 uppercase mb-1">Title</label>
            <input type="text" class="w-full h-9 px-4 border border-rule rounded-card font-body text-sm focus:ring-2 focus:ring-accent">
        </div>
        <div>
            <label class="block text-[12px] font-body font-medium text-ink2 uppercase mb-2">Type</label>
            <div class="flex gap-0 border border-rule rounded-card overflow-hidden">
                <button class="flex-1 py-2 px-4 text-[13px] font-body {{ $type === 'video' ? 'bg-ink text-white' : 'bg-surface text-ink2 border-r border-rule' }}">Video</button>
                <button class="flex-1 py-2 px-4 text-[13px] font-body {{ $type === 'text' ? 'bg-ink text-white' : 'bg-surface text-ink2 border-r border-rule' }}">Text</button>
                <button class="flex-1 py-2 px-4 text-[13px] font-body {{ $type === 'pdf' ? 'bg-ink text-white' : 'bg-surface text-ink2' }}">PDF</button>
            </div>
        </div>
        <div class="border border-rule rounded-card p-4">
            <label class="block text-[12px] font-body font-medium text-ink2 uppercase mb-1">Video URL</label>
            <input type="url" placeholder="https://..." class="w-full h-9 px-4 border border-rule rounded-card font-body text-sm focus:ring-2 focus:ring-accent">
            <p class="text-[12px] text-ink3 mt-1">or <a href="#" class="text-accent hover:underline">upload</a></p>
        </div>
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2 text-[13px] font-body">
                <input type="checkbox" class="rounded border-rule text-accent focus:ring-accent"> Is preview
            </label>
            <div class="flex items-center gap-2">
                <label class="text-[13px] font-body">Unlock after</label>
                <input type="number" value="0" class="w-16 h-9 px-2 border border-rule rounded-card text-sm"> days
            </div>
        </div>
    </div>
    <div class="mt-6 flex justify-between">
        <a href="#" class="text-accent text-[13px] font-body hover:underline">Back to curriculum</a>
        <button class="px-5 py-2.5 bg-ink text-white font-display font-bold text-sm rounded-card">Save</button>
    </div>
</div>
