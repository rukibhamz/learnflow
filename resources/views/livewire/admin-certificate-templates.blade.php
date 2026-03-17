<div>
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-display font-bold text-lg text-ink">Certificate Templates</h2>
            <p class="text-sm text-ink3">Manage PDF certificate designs. Use <code class="text-xs bg-bg px-1 py-0.5 rounded">@{{variable}}</code> placeholders in HTML.</p>
        </div>
        <button wire:click="openEditor" class="px-4 py-2 bg-ink text-white text-sm font-bold rounded-card hover:opacity-90">+ New Template</button>
    </div>

    <div class="text-xs text-ink3 mb-4">
        Available variables: <code>student_name</code>, <code>course_title</code>, <code>instructor_name</code>, <code>issued_date</code>, <code>certificate_uuid</code>, <code>verify_url</code>
    </div>

    <div class="space-y-3">
        @forelse($templates as $t)
            <div wire:key="tpl-{{ $t->id }}" class="bg-surface border border-rule rounded-xl p-5 flex items-center justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-display font-bold text-sm text-ink">{{ $t->name }}</span>
                        @if($t->is_default)
                            <span class="px-2 py-0.5 bg-primary/10 text-primary text-[10px] font-bold uppercase rounded">Default</span>
                        @endif
                        <span class="px-2 py-0.5 {{ $t->is_active ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }} text-[10px] font-bold uppercase rounded">
                            {{ $t->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <p class="text-xs text-ink3">{{ $t->orientation }} · {{ strtoupper($t->paper_size) }} · {{ Str::limit($t->description, 80) }}</p>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    @if(!$t->is_default)
                        <button wire:click="setDefault({{ $t->id }})" class="px-2 py-1 text-[10px] font-bold border border-rule rounded hover:bg-bg" title="Set as default">Set Default</button>
                    @endif
                    <button wire:click="toggleActive({{ $t->id }})" class="p-1.5 text-ink3 hover:text-primary" title="Toggle active">
                        <span class="material-symbols-outlined text-[18px]">{{ $t->is_active ? 'visibility_off' : 'visibility' }}</span>
                    </button>
                    <button wire:click="openEditor({{ $t->id }})" class="p-1.5 text-ink3 hover:text-primary" title="Edit">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                    <button wire:click="delete({{ $t->id }})" wire:confirm="Delete this template?" class="p-1.5 text-ink3 hover:text-red-600" title="Delete">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-ink3 text-sm">No templates yet. Create your first one above.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $templates->links() }}</div>

    {{-- Editor Modal --}}
    @if($showEditor)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click.self="$wire.closeEditor()">
            <div class="bg-surface rounded-xl w-full max-w-3xl p-6 border border-rule max-h-[90vh] overflow-y-auto">
                <h3 class="font-display font-bold text-lg text-ink mb-4">{{ $editingId ? 'Edit' : 'Create' }} Template</h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Name *</label>
                            <input type="text" wire:model="name" class="w-full h-10 px-3 border border-rule rounded-card text-sm" placeholder="e.g. Classic Certificate">
                            @error('name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Description</label>
                            <input type="text" wire:model="description" class="w-full h-10 px-3 border border-rule rounded-card text-sm" placeholder="Optional">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Orientation</label>
                            <select wire:model="orientation" class="w-full h-10 px-3 border border-rule rounded-card text-sm bg-surface">
                                <option value="landscape">Landscape</option>
                                <option value="portrait">Portrait</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">Paper Size</label>
                            <select wire:model="paper_size" class="w-full h-10 px-3 border border-rule rounded-card text-sm bg-surface">
                                <option value="a4">A4</option>
                                <option value="letter">Letter</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-4">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" wire:model="is_default" class="rounded border-rule text-primary">
                                <span class="text-sm">Default</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" wire:model="is_active" class="rounded border-rule text-primary">
                                <span class="text-sm">Active</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-1">HTML Template *</label>
                        <textarea wire:model="html_template" rows="16" class="w-full px-4 py-3 border border-rule rounded-card text-xs font-mono resize-y focus:outline-none focus:border-primary" spellcheck="false"></textarea>
                        @error('html_template') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="closeEditor" class="px-4 py-2 border border-rule rounded-card text-sm text-ink2 hover:bg-bg">Cancel</button>
                    <button wire:click="save" class="px-4 py-2 bg-ink text-white text-sm font-bold rounded-card hover:opacity-90">{{ $editingId ? 'Update' : 'Create Template' }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
