<div>
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-4 mb-8">
        <div class="relative flex-1 max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink3">search</span>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search blog posts..." 
                class="w-full h-11 bg-surface border border-rule rounded-lg pl-10 pr-4 font-body text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all">
        </div>
        <button wire:click="create" class="h-11 px-5 bg-primary text-white font-poppins font-bold text-sm rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Add Post
        </button>
    </div>

    <div class="bg-surface border border-rule rounded-none overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-background-light text-[11px] font-poppins font-bold uppercase tracking-widest text-ink3 border-b border-rule">
                <tr>
                    <th class="px-6 h-[44px]">Featured</th>
                    <th class="px-6 h-[44px]">Title</th>
                    <th class="px-6 h-[44px] text-center">Status</th>
                    <th class="px-6 h-[44px] text-right">Published At</th>
                    <th class="px-6 h-[44px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-[13px] font-body">
                @forelse($posts as $post)
                <tr class="border-b border-rule last:border-0 hover:bg-background-light/30 transition-colors">
                    <td class="px-6 py-4">
                        @if($post->getFirstMediaUrl('featured_image'))
                            <img src="{{ $post->getFirstMediaUrl('featured_image') }}" class="w-16 h-12 object-cover rounded border border-rule">
                        @else
                            <div class="w-16 h-12 bg-bg border border-rule rounded flex items-center justify-center text-ink3">
                                <span class="material-symbols-outlined text-[20px]">image</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-medium text-ink block">{{ $post->title }}</span>
                        <span class="text-[11px] text-ink2">{{ $post->slug }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button wire:click="togglePublish({{ $post->id }})" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $post->is_published ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-gray-50 text-gray-600 border border-gray-200' }}">
                            {{ $post->is_published ? 'Published' : 'Draft' }}
                        </button>
                    </td>
                    <td class="px-6 py-4 text-right text-ink3">{{ $post->published_at ? $post->published_at->format('M j, Y') : '-' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button wire:click="edit({{ $post->id }})" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Edit Post">
                                <span class="material-symbols-outlined text-[18px]">edit_square</span>
                            </button>
                            <button wire:click="delete({{ $post->id }})" wire:confirm="Are you sure you want to delete this post?" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-red-500 transition-colors" title="Delete Post">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-ink3">No blog posts found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $posts->links() }}
    </div>

    {{-- Form Modal --}}
    @if($showForm)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-[100] overflow-y-auto" wire:click.self="$set('showForm', false)">
        <div class="bg-surface rounded-lg shadow-xl w-full max-w-4xl p-8 my-8 relative">
            <button wire:click="$set('showForm', false)" class="absolute top-6 right-6 text-ink3 hover:text-ink transition-colors">
                <span class="material-symbols-outlined text-[24px]">close</span>
            </button>
            
            <h3 class="font-poppins font-bold text-2xl text-ink mb-8">{{ $postId ? 'Edit Post' : 'Create New Post' }}</h3>
            
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-ink mb-2">Title</label>
                        <input type="text" wire:model.live.debounce.500ms="title" class="w-full h-12 bg-bg border border-rule rounded-lg px-4 text-base focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body text-ink" placeholder="Enter post title" required>
                        @error('title') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-ink mb-2">Slug</label>
                        <input type="text" wire:model="slug" class="w-full h-12 bg-bg border border-rule rounded-lg px-4 text-base text-ink focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body" placeholder="URL-friendly-slug" required>
                        @error('slug') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-ink mb-2">Excerpt (Optional)</label>
                    <textarea wire:model="excerpt" rows="2" class="w-full bg-bg border border-rule rounded-lg p-4 text-base text-ink focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body" placeholder="Short summary of the post..."></textarea>
                    @error('excerpt') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-ink mb-2">Content</label>
                    <textarea wire:model="content" rows="10" class="w-full bg-bg border border-rule rounded-lg p-4 text-base text-ink focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body" placeholder="Write your content here..." required></textarea>
                    @error('content') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                    <div>
                        <label class="block text-sm font-bold text-ink mb-2">Featured Image</label>
                        <input type="file" wire:model="image" class="w-full text-sm text-ink2 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90 cursor-pointer">
                        @error('image') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_published" class="sr-only peer">
                            <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            <span class="ms-3 text-sm font-bold text-ink">Publish immediately</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-rule mt-8">
                    <button type="button" wire:click="$set('showForm', false)" class="px-6 py-3 border text-base border-rule rounded-lg font-medium text-ink2 hover:bg-bg transition-colors">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-primary text-white text-base rounded-lg font-bold hover:opacity-90 transition-opacity">
                        <span wire:loading.remove wire:target="image, save">Save Post</span>
                        <span wire:loading wire:target="image, save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
