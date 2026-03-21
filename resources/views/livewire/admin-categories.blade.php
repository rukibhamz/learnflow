<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-display font-bold text-ink">Course Categories</h1>
            <p class="text-sm text-ink3 mt-1">Manage and organize your course categories.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-ink text-white rounded-lg text-sm font-bold hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">add</span>
            New Category
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-surface border border-rule rounded-xl overflow-hidden">
        <div class="p-6 border-b border-rule flex items-center gap-4">
            <div class="relative flex-1 max-w-md">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink3">search</span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search categories..." 
                    class="w-full h-10 bg-bg border border-rule rounded-lg pl-10 pr-4 text-sm focus:outline-none focus:border-primary">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-background-light border-b border-rule">
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-ink3">ID</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-ink3">Category Name</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-ink3">Slug</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-ink3">Icon</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-ink3 text-center">Courses</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-ink3 text-center">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-ink3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rule">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-background-light/50 transition-colors">
                            <td class="px-6 py-4 text-sm text-ink3">#{{ $cat->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-sm text-ink">{{ $cat->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-ink3">{{ $cat->slug }}</td>
                            <td class="px-6 py-4">
                                @if($cat->icon)
                                    <span class="material-symbols-outlined text-ink3">{{ $cat->icon }}</span>
                                @else
                                    <span class="text-xs text-ink3 italic">No icon</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-ink text-center">
                                <span class="px-2 py-0.5 bg-bg border border-rule rounded-full text-xs">
                                    {{ $cat->courses_count ?? $cat->courses()->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="toggleStatus({{ $cat->id }})" class="focus:outline-none">
                                    @if($cat->is_active)
                                        <span class="px-2 py-1 bg-green-50 text-green-700 text-[10px] font-bold uppercase tracking-wider rounded">Active</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold uppercase tracking-wider rounded">Inactive</span>
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $cat) }}" class="p-2 text-ink3 hover:text-primary transition-colors" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <button wire:click="delete({{ $cat->id }})" wire:confirm="Are you sure you want to delete this category?" 
                                        class="p-2 text-ink3 hover:text-red-500 transition-colors" title="Delete">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-[48px] text-ink3 mb-2">category</span>
                                    <p class="text-sm text-ink3">No categories found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-rule">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
