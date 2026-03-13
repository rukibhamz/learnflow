<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-surface border border-rule rounded-card p-5">
            <h2 class="font-display font-bold text-ink mb-4">Basic info</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-[12px] font-body font-medium text-ink2 uppercase tracking-wide mb-1">Title</label>
                    <input type="text" wire:model="title" class="w-full h-9 px-4 border border-rule rounded-card font-body text-sm focus:ring-2 focus:ring-accent">
                </div>
                <div>
                    <label class="block text-[12px] font-body font-medium text-ink2 uppercase tracking-wide mb-1">Slug</label>
                    <input type="text" wire:model="slug" class="w-full h-9 px-4 border border-rule rounded-card font-body text-sm focus:ring-2 focus:ring-accent" placeholder="auto-filled">
                </div>
                <div>
                    <label class="block text-[12px] font-body font-medium text-ink2 uppercase tracking-wide mb-1">Short description</label>
                    <textarea rows="3" wire:model="shortDescription" class="w-full px-4 py-3 border border-rule rounded-card font-body text-sm focus:ring-2 focus:ring-accent"></textarea>
                </div>
            </div>
        </div>
        <div class="bg-surface border border-rule rounded-card p-5">
            <h2 class="font-display font-bold text-ink mb-4">Description</h2>
            <div class="border border-rule rounded-card min-h-[120px] p-4 text-ink3 text-sm font-body">
                Rich text editor (Trix)
            </div>
        </div>
        <div class="bg-surface border border-rule rounded-card p-5">
            <h2 class="font-display font-bold text-ink mb-4">Thumbnail</h2>
            <div class="border border-dashed border-rule rounded-card p-8 text-center text-ink3 text-sm">
                Drag and drop or click to upload
            </div>
        </div>
        <div class="bg-surface border border-rule rounded-card p-5">
            <h2 class="font-display font-bold text-ink mb-4">Details</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[12px] font-body font-medium text-ink2 uppercase mb-1">Price ($)</label>
                    <input type="number" class="w-full h-9 px-4 border border-rule rounded-card font-body text-sm focus:ring-2 focus:ring-accent">
                </div>
                <div>
                    <label class="block text-[12px] font-body font-medium text-ink2 uppercase mb-1">Level</label>
                    <select class="w-full h-9 px-4 border border-rule rounded-card font-body text-sm focus:ring-2 focus:ring-accent bg-surface">
                        <option>Beginner</option><option>Intermediate</option><option>Advanced</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="lg:col-span-1">
        <div class="sticky top-20 bg-surface border border-rule rounded-card p-5">
            <div class="mb-4">@include('components.status-badge', ['status' => $status])</div>
            <button class="w-full py-2 mb-2 bg-ink text-white font-display font-bold text-sm rounded-card">Submit for review</button>
            <p class="text-[12px] text-ink3 mt-4">Autosaved 2 min ago</p>
            <hr class="border-t border-rule my-4">
            <a href="#" class="text-red-600 text-[13px] font-body hover:underline">Delete course</a>
        </div>
    </div>
</div>
