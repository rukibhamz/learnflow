<div>
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-xl font-bold font-display text-ink">Hero Section Slides</h2>
            <p class="text-sm text-ink2 mt-1">Manage the dynamic carousel slides displayed on the homepage.</p>
        </div>
        <button wire:click="create" class="h-11 px-5 bg-primary text-white font-poppins font-bold text-sm rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Add Slide
        </button>
    </div>

    <div class="bg-surface border border-rule rounded-none overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-background-light text-[11px] font-poppins font-bold uppercase tracking-widest text-ink3 border-b border-rule">
                <tr>
                    <th class="px-6 h-[44px] w-16">Order</th>
                    <th class="px-6 h-[44px]">Image</th>
                    <th class="px-6 h-[44px]">Content Block</th>
                    <th class="px-6 h-[44px] text-center">Status</th>
                    <th class="px-6 h-[44px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-[13px] font-body">
                @forelse($slides as $index => $slide)
                <tr class="border-b border-rule last:border-0 hover:bg-background-light/30 transition-colors {{ !$slide->is_active ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex flex-col items-center gap-1">
                            @if($index > 0)
                                <button wire:click="moveUp({{ $slide->id }})" class="text-ink3 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">expand_less</span>
                                </button>
                            @else
                                <span class="w-4 h-4 block"></span>
                            @endif
                            <span class="font-bold text-ink2">{{ $slide->order }}</span>
                            @if($index < count($slides) - 1)
                                <button wire:click="moveDown({{ $slide->id }})" class="text-ink3 hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">expand_more</span>
                                </button>
                            @else
                                <span class="w-4 h-4 block"></span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($slide->getFirstMediaUrl('background'))
                            <img src="{{ $slide->getFirstMediaUrl('background') }}" class="w-24 h-16 object-cover rounded border border-rule">
                        @else
                            <div class="w-24 h-16 bg-bg border border-rule rounded flex items-center justify-center text-ink3">
                                <span class="material-symbols-outlined text-[20px]">image</span>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[10px] font-bold uppercase text-accent tracking-widest">{{ $slide->tag }}</span>
                        <span class="font-bold text-ink block text-sm mt-1">{!! strip_tags($slide->title) !!}</span>
                        @if($slide->button_text)
                            <span class="text-[11px] text-ink2 mt-1 block">Button: {{ $slide->button_text }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button wire:click="toggleActive({{ $slide->id }})" class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $slide->is_active ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-gray-50 text-gray-600 border border-gray-200' }}">
                            {{ $slide->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button wire:click="edit({{ $slide->id }})" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Edit Slide">
                                <span class="material-symbols-outlined text-[18px]">edit_square</span>
                            </button>
                            <button wire:click="delete({{ $slide->id }})" wire:confirm="Are you sure you want to delete this slide?" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-red-500 transition-colors" title="Delete Slide">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-ink3">No hero slides added yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Form Modal --}}
    @if($showForm)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-[100] overflow-y-auto" wire:click.self="cancel">
        <div class="bg-surface rounded-lg shadow-xl w-full max-w-3xl p-8 my-8 relative">
            <button wire:click="cancel" class="absolute top-6 right-6 text-ink3 hover:text-ink transition-colors">
                <span class="material-symbols-outlined text-[24px]">close</span>
            </button>
            
            <h3 class="font-poppins font-bold text-2xl text-ink mb-8">{{ $slideId ? 'Edit Slide' : 'Add New Slide' }}</h3>
            
            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-ink mb-2">Heading Title (HTML allowed)</label>
                        <input type="text" wire:model="title" class="w-full h-12 bg-bg border border-rule rounded-lg px-4 text-base focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body text-ink" placeholder="E.g., Learn without <span class='text-accent'>limits.</span>" required>
                        <span class="text-[11px] text-ink3 mt-1 block">Use HTML tags like &lt;span class='text-accent'&gt; &lt;/span&gt; for highlighted words.</span>
                        @error('title') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-ink mb-2">Overhead Tag (Optional)</label>
                        <input type="text" wire:model="tag" class="w-full h-12 bg-bg border border-rule rounded-lg px-4 text-base text-ink focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body" placeholder="E.g., Online Learning Platform">
                        @error('tag') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-ink mb-2">Description Paragraph (Optional)</label>
                    <textarea wire:model="description" rows="3" class="w-full bg-bg border border-rule rounded-lg p-4 text-base text-ink focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-body" placeholder="Experience the future of education..."></textarea>
                    @error('description') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                    <div>
                        <label class="block text-sm font-bold text-ink mb-2">Background Image</label>
                        <input type="file" wire:model="image" class="w-full text-sm text-ink2 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90 cursor-pointer">
                        @error('image') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="w-11 h-6 bg-rule peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            <span class="ms-3 text-sm font-bold text-ink">Slide Active</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-rule mt-8">
                    <button type="button" wire:click="cancel" class="px-6 py-3 border text-base border-rule rounded-lg font-medium text-ink2 hover:bg-bg transition-colors">Cancel</button>
                    <button type="submit" class="px-6 py-3 bg-primary text-white text-base rounded-lg font-bold hover:opacity-90 transition-opacity">
                        <span wire:loading.remove wire:target="image, save">Save Slide</span>
                        <span wire:loading wire:target="image, save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
