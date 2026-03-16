<div>
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-8">
        <div class="bg-surface border border-rule rounded-lg p-6 space-y-6">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Basic Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Course Title</label>
                    <input type="text" wire:model.live.debounce.500ms="title" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" placeholder="e.g. Complete Web Development Bootcamp" required>
                    @error('title') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">URL Slug</label>
                    <div class="flex items-center">
                        <span class="text-sm text-ink3 mr-2">/courses/</span>
                        <input type="text" wire:model="slug" class="flex-1 h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" placeholder="course-slug" required>
                    </div>
                    @error('slug') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Short Description</label>
                    <textarea wire:model="short_description" rows="2" class="w-full bg-bg border border-rule rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-primary resize-none" placeholder="Brief summary for course cards (max 500 chars)"></textarea>
                    @error('short_description') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Full Description</label>
                    <textarea wire:model="description" rows="6" class="w-full bg-bg border border-rule rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-primary resize-none" placeholder="Detailed course description..."></textarea>
                    @error('description') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="bg-surface border border-rule rounded-lg p-6 space-y-6">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Course Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Price (USD)</label>
                    <input type="number" wire:model="price" step="0.01" min="0" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" placeholder="0.00">
                    @error('price') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Level</label>
                    <select wire:model="level" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                    @error('level') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Category</label>
                    <input type="text" wire:model="category" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" placeholder="e.g. Development">
                    @error('category') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Language</label>
                    <select wire:model="language" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                        <option value="de">German</option>
                        <option value="pt">Portuguese</option>
                    </select>
                    @error('language') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('instructor.courses.index') }}" class="text-sm text-ink2 hover:text-ink transition-colors">
                &larr; Back to Courses
            </a>
            <button type="submit" class="px-6 py-3 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90 transition-opacity">
                {{ $course ? 'Save Changes' : 'Create Course' }}
            </button>
        </div>
    </form>
</div>
