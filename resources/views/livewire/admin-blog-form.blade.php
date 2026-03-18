<div class="bg-surface rounded-none border border-rule p-8 shadow-sm">
    <div class="flex items-center justify-between mb-8">
        <h3 class="font-poppins font-bold text-2xl text-ink">{{ $postId ? 'Edit Post' : 'Create New Post' }}</h3>
        <a href="{{ route('admin.blogs.index') }}" class="text-sm font-bold text-ink3 hover:text-ink transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Back to List
        </a>
    </div>

    <form wire:submit.prevent="save" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
                <label class="block text-sm font-bold text-ink">Title</label>
                <input type="text" wire:model.live.debounce.500ms="title" class="w-full h-12 bg-bg border border-rule rounded-lg px-4 text-base focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body text-ink" placeholder="Enter post title" required>
                @error('title') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div class="space-y-2">
                <label class="block text-sm font-bold text-ink">Slug</label>
                <input type="text" wire:model="slug" class="w-full h-12 bg-bg border border-rule rounded-lg px-4 text-base text-ink focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body" placeholder="URL-friendly-slug" required>
                @error('slug') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-bold text-ink">Excerpt (Optional)</label>
            <textarea wire:model="excerpt" rows="3" class="w-full bg-bg border border-rule rounded-lg p-4 text-base text-ink focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body" placeholder="Short summary of the post for the listing page..."></textarea>
            @error('excerpt') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="block text-sm font-bold text-ink mb-2">Content</label>
            <div wire:ignore
                 x-data="{ 
                    content: @entangle('content'),
                    isFocused() { return document.activeElement === this.$refs.trix }
                 }"
                 x-init="
                    if (content) {
                        $refs.trix.editor.loadHTML(content);
                    }
                    $watch('content', value => {
                        if (!isFocused() && value !== $refs.trix.editor.getHTML()) {
                            $refs.trix.editor.loadHTML(value || '');
                        }
                    })
                 "
                 x-on:trix-change="content = $event.target.value"
                 class="min-h-[700px]">
                <trix-editor x-ref="trix" 
                             class="trix-content bg-bg border border-rule rounded-lg p-4 min-h-[700px] text-base text-ink focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body">
                </trix-editor>
            </div>
            @error('content') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <div class="space-y-4">
                <label class="block text-sm font-bold text-ink">Featured Image</label>
                @if($existingImage && !$image)
                    <div class="mb-4 relative group w-48">
                        <img src="{{ $existingImage }}" class="w-full aspect-video object-cover rounded-lg border border-rule">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                            <span class="text-white text-[10px] font-bold uppercase tracking-wider">Current Image</span>
                        </div>
                    </div>
                @endif
                <input type="file" wire:model="image" class="w-full text-sm text-ink2 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90 cursor-pointer">
                @error('image') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex items-center gap-4 pt-10">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="is_published" class="sr-only peer">
                    <div class="w-14 h-7 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    <span class="ms-4 text-base font-bold text-ink">Active/Published</span>
                </label>
                <div class="text-xs text-ink3">
                    <p>Drafts are only visible to admins.</p>
                </div>
            </div>
        </div>

        <div class="pt-10 border-t border-rule mt-10">
            <h4 class="font-poppins font-bold text-lg text-ink mb-6">SEO Optimization</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-ink">Meta Description</label>
                    <textarea wire:model="meta_description" rows="3" class="w-full bg-bg border border-rule rounded-lg p-4 text-base text-ink focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body" placeholder="Max 160 characters for best results in search engines..."></textarea>
                    @error('meta_description') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-ink">Keywords</label>
                    <input type="text" wire:model="keywords" class="w-full h-12 bg-bg border border-rule rounded-lg px-4 text-base text-ink focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body" placeholder="e.g. learning, education, platform (comma separated)">
                    <p class="text-[10px] text-ink3 mt-2">Separate multiple keywords with commas.</p>
                    @error('keywords') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 pt-10 border-t border-rule">
            <a href="{{ route('admin.blogs.index') }}" class="px-8 py-4 border-2 border-rule rounded-lg font-bold text-ink2 hover:bg-bg transition-colors">Discard Changes</a>
            <button type="submit" class="px-10 py-4 bg-primary text-white rounded-lg font-bold hover:opacity-90 transition-opacity flex items-center gap-2 shadow-lg shadow-primary/20">
                <span wire:loading.remove wire:target="image, save">Save Blog Post</span>
                <span wire:loading wire:target="image, save">Processing...</span>
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
            </button>
        </div>
    </form>
</div>
