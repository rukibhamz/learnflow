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
        <a href="{{ route('admin.blogs.create') }}" class="h-11 px-5 bg-primary text-white font-poppins font-bold text-sm rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Add Post
        </a>
    </div>

    <div class="bg-surface border border-rule rounded-none overflow-hidden shadow-sm">
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
                            <a href="{{ route('admin.blogs.edit', $post->id) }}" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Edit Post">
                                <span class="material-symbols-outlined text-[18px]">edit_square</span>
                            </a>
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
</div>
