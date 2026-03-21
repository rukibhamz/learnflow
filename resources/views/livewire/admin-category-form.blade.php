<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-display font-bold text-ink">{{ $category ? 'Edit' : 'New' }} Category</h1>
            <p class="text-sm text-ink3 mt-1">Configure your course category details.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-ink2 hover:text-ink transition-colors">
            ← Back to Categories
        </a>
    </div>

    <form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Info --}}
                <div class="bg-surface border border-rule rounded-xl p-6 space-y-6">
                    <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Category Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Category Name *</label>
                            <input type="text" wire:model.live.debounce.500ms="name" 
                                class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                                placeholder="e.g. Graphic Design" required>
                            @error('name') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">URL Slug *</label>
                            <input type="text" wire:model="slug" 
                                class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                                placeholder="graphic-design" required>
                            @error('slug') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Icon (Material Symbols Name)</label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-ink3">{{ $icon ?: 'help' }}</span>
                                <input type="text" wire:model.live="icon" 
                                    class="w-full h-11 bg-bg border border-rule rounded-lg pl-10 pr-4 text-sm focus:outline-none focus:border-primary" 
                                    placeholder="e.g. palette">
                            </div>
                            <p class="text-[10px] text-ink3 mt-1">Use <a href="https://fonts.google.com/icons?icon.set=Material+Symbols" target="_blank" class="text-primary hover:underline">Material Symbols</a> names.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Description</label>
                        <textarea wire:model="description" rows="4" 
                            class="w-full bg-bg border border-rule rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-primary resize-none" 
                            placeholder="Brief description of this category..."></textarea>
                        @error('description') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Sidebar Settings --}}
            <div class="space-y-6">
                <div class="bg-surface border border-rule rounded-xl p-6 space-y-6">
                    <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Settings</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="text-sm font-bold text-ink">Active Status</label>
                                <p class="text-[11px] text-ink3">Visible in filters and course forms</p>
                            </div>
                            <button type="button" 
                                wire:click="$toggle('is_active')"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $is_active ? 'bg-primary' : 'bg-rule' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Display Order</label>
                            <input type="number" wire:model="order" 
                                class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                                placeholder="0">
                            @error('order') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-surface border border-rule rounded-xl p-6">
                    <button type="submit" class="w-full py-3 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90 transition-opacity">
                        {{ $category ? 'Update Category' : 'Create Category' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
