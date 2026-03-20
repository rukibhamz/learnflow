<div x-data="{ 
    autoSaveInterval: null,
    init() {
        this.autoSaveInterval = setInterval(() => {
            $wire.autosave();
        }, 60000);
    },
    destroy() {
        if (this.autoSaveInterval) clearInterval(this.autoSaveInterval);
    }
}" x-init="init()" @beforeunload.window="destroy()">
    
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Status Bar --}}
    <div class="mb-6 flex items-center justify-between bg-surface border border-rule rounded-lg p-4">
        <div class="flex items-center gap-4">
            @if($course)
                @php
                    $statusColors = [
                        'draft' => 'bg-gray-100 text-gray-600',
                        'review' => 'bg-amber-50 text-amber-600',
                        'published' => 'bg-green-50 text-green-600',
                        'archived' => 'bg-red-50 text-red-600',
                    ];
                    $statusColor = $statusColors[$course->status->value] ?? $statusColors['draft'];
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $statusColor }}">
                    {{ ucfirst($course->status->value) }}
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-gray-100 text-gray-600">
                    New Course
                </span>
            @endif
            
            @if($lastSavedAt)
                <span class="text-xs text-ink3">Last saved {{ $lastSavedAt->diffForHumans() }}</span>
            @endif
            
            @if($isDirty)
                <span class="text-xs text-amber-600 flex items-center gap-1">
                    <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                    Unsaved changes
                </span>
            @endif
        </div>
        
        <div class="flex items-center gap-2">
            <button type="button" wire:click="saveDraft" class="px-4 py-2 border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-bg transition-colors">
                Save Draft
            </button>
            
            @if($course)
                @if($course->status->value === 'draft')
                    <button type="button" wire:click="submitForReview" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-bold hover:bg-blue-600 transition-colors">
                        Submit for Review
                    </button>
                @elseif($course->status->value === 'review')
                    <span class="px-4 py-2 text-sm text-ink3">Awaiting admin approval</span>
                @elseif($course->status->value === 'published')
                    <button type="button" wire:click="unpublish" wire:confirm="Unpublish this course?" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-bold hover:bg-amber-600 transition-colors">
                        Unpublish
                    </button>
                @endif
            @endif
        </div>
    </div>

    <form wire:submit="saveDraft" class="space-y-8">
        {{-- Basic Information --}}
        <div class="bg-surface border border-rule rounded-lg p-6 space-y-6">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Basic Information</h3>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Course Title *</label>
                        <input type="text" wire:model.live.debounce.500ms="title" 
                            class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                            placeholder="e.g. Complete Web Development Bootcamp" required>
                        @error('title') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">URL Slug *</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-ink3 shrink-0">/courses/</span>
                            <input type="text" wire:model="slug" 
                                class="flex-1 h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                                placeholder="course-slug" required>
                        </div>
                        @error('slug') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Short Description</label>
                        <textarea wire:model="short_description" rows="2" maxlength="500"
                            class="w-full bg-bg border border-rule rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-primary resize-none" 
                            placeholder="Brief summary for course cards (max 500 chars)"></textarea>
                        <div class="text-right text-xs text-ink3 mt-1">{{ strlen($short_description) }}/500</div>
                        @error('short_description') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Thumbnail Upload --}}
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Thumbnail</label>
                    <div class="border-2 border-dashed border-rule rounded-lg p-4 text-center"
                         x-data="{ dragging: false }"
                         x-on:dragover.prevent="dragging = true"
                         x-on:dragleave.prevent="dragging = false"
                         x-on:drop.prevent="dragging = false; $refs.thumbnailInput.files = $event.dataTransfer.files; $refs.thumbnailInput.dispatchEvent(new Event('change'))"
                         :class="{ 'border-primary bg-primary/5': dragging }">
                        
                        @if($thumbnail)
                            <div class="relative">
                                <img src="{{ $thumbnail->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg mb-2" alt="Preview">
                                <button type="button" wire:click="$set('thumbnail', null)" class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>
                        @elseif($existingThumbnail)
                            <div class="relative">
                                <img src="{{ $existingThumbnail }}" class="w-full h-32 object-cover rounded-lg mb-2" alt="Current thumbnail">
                                <button type="button" wire:click="removeThumbnail" class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                </button>
                            </div>
                        @else
                            <span class="material-symbols-outlined text-[48px] text-ink3 mb-2">image</span>
                            <p class="text-xs text-ink3 mb-2">Drag & drop or click to upload</p>
                        @endif
                        
                        <input type="file" wire:model="thumbnail" accept="image/*" class="hidden" x-ref="thumbnailInput" id="thumbnail-input">
                        <label for="thumbnail-input" class="inline-block px-4 py-2 bg-bg border border-rule rounded-lg text-xs font-medium text-ink2 cursor-pointer hover:bg-background-light transition-colors">
                            Choose File
                        </label>
                        
                        <div wire:loading wire:target="thumbnail" class="mt-2 text-xs text-primary">Uploading...</div>
                    </div>
                    @error('thumbnail') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Full Description with Rich Text --}}
        <div class="bg-surface border border-rule rounded-lg p-6 space-y-4">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Full Description</h3>
            <p class="text-xs text-ink3">Write a detailed course description. HTML formatting is supported.</p>

            <div x-data="{ 
                content: @entangle('description'),
                useFallback: false,
                init() {
                    this.$nextTick(() => {
                        if (typeof Quill === 'undefined') {
                            this.useFallback = true;
                            return;
                        }
                        try {
                            const quill = new Quill(this.$refs.editor, {
                                theme: 'snow',
                                placeholder: 'Write a detailed course description...',
                                modules: {
                                    toolbar: [
                                        [{ 'header': [1, 2, 3, false] }],
                                        ['bold', 'italic', 'underline', 'strike'],
                                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                        ['blockquote', 'code-block'],
                                        ['link'],
                                        ['clean']
                                    ]
                                }
                            });
                            quill.root.innerHTML = this.content || '';
                            quill.on('text-change', () => {
                                this.content = quill.root.innerHTML;
                            });
                        } catch (e) {
                            this.useFallback = true;
                        }
                    });
                }
            }">
                <div x-show="!useFallback" x-cloak>
                    <div x-ref="editor" class="bg-bg border border-rule rounded-lg min-h-[200px] [&_.ql-editor]:min-h-[180px]"></div>
                </div>
                <div x-show="useFallback" x-cloak>
                    <textarea wire:model.live.debounce.300ms="description" rows="8"
                        class="w-full bg-bg border border-rule rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-primary font-body text-ink resize-y"
                        placeholder="Write a detailed course description..."></textarea>
                </div>
            </div>
            @error('description') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        {{-- Course Details --}}
        <div class="bg-surface border border-rule rounded-lg p-6 space-y-6">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Course Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Price (USD) *</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-ink3">$</span>
                        <input type="number" wire:model="price" step="0.01" min="0" 
                            class="w-full h-11 bg-bg border border-rule rounded-lg pl-8 pr-4 text-sm focus:outline-none focus:border-primary" 
                            placeholder="0.00">
                    </div>
                    <p class="text-[10px] text-ink3 mt-1">Set to 0 for free courses</p>
                    @error('price') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Level *</label>
                    <select wire:model="level" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                    @error('level') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Language</label>
                    <select wire:model="language" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                        <option value="de">German</option>
                        <option value="pt">Portuguese</option>
                        <option value="zh">Chinese</option>
                        <option value="ja">Japanese</option>
                        <option value="ko">Korean</option>
                    </select>
                    @error('language') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Category</label>
                    <input type="text" wire:model="category" 
                        class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                        placeholder="e.g. Development">
                    @error('category') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Requirements --}}
        <div class="bg-surface border border-rule rounded-lg p-6 space-y-4">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Requirements</h3>
            <p class="text-xs text-ink3">What should students know before taking this course?</p>
            
            <div class="space-y-2">
                @foreach($requirements as $index => $requirement)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-ink3 text-[18px]">check_circle</span>
                        <span class="flex-1 text-sm text-ink">{{ $requirement }}</span>
                        <button type="button" wire:click="removeRequirement({{ $index }})" class="p-1 text-ink3 hover:text-red-500 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                @endforeach
            </div>
            
            <div class="flex gap-2">
                <input type="text" wire:model="newRequirement" wire:keydown.enter.prevent="addRequirement"
                    class="flex-1 h-10 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                    placeholder="Add a requirement...">
                <button type="button" wire:click="addRequirement" class="px-4 h-10 bg-bg border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-background-light transition-colors">
                    Add
                </button>
            </div>
        </div>

        {{-- Learning Outcomes --}}
        <div class="bg-surface border border-rule rounded-lg p-6 space-y-4">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Learning Outcomes</h3>
            <p class="text-xs text-ink3">What will students learn from this course?</p>
            
            <div class="space-y-2">
                @foreach($outcomes as $index => $outcome)
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-500 text-[18px]">school</span>
                        <span class="flex-1 text-sm text-ink">{{ $outcome }}</span>
                        <button type="button" wire:click="removeOutcome({{ $index }})" class="p-1 text-ink3 hover:text-red-500 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>
                @endforeach
            </div>
            
            <div class="flex gap-2">
                <input type="text" wire:model="newOutcome" wire:keydown.enter.prevent="addOutcome"
                    class="flex-1 h-10 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                    placeholder="Add a learning outcome...">
                <button type="button" wire:click="addOutcome" class="px-4 h-10 bg-bg border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-background-light transition-colors">
                    Add
                </button>
            </div>
        </div>

        {{-- Prerequisites --}}
        <div class="bg-surface border border-rule rounded-lg p-6 space-y-4">
            <h3 class="font-display font-bold text-sm text-ink uppercase tracking-widest">Prerequisites</h3>
            <p class="text-xs text-ink3">Select courses that students must complete before enrolling in this one.</p>

            @php
                $availableCourses = \App\Models\Course::query()
                    ->where('instructor_id', auth()->id())
                    ->when($course, fn ($q) => $q->where('id', '!=', $course->id))
                    ->published()
                    ->orderBy('title')
                    ->get(['id', 'title']);
            @endphp

            @if($availableCourses->isEmpty())
                <p class="text-xs text-ink3 italic">No other published courses available to set as prerequisites.</p>
            @else
                <div class="space-y-2">
                    @foreach($availableCourses as $prereq)
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-rule hover:bg-bg transition-colors cursor-pointer {{ in_array($prereq->id, $prerequisite_ids) ? 'bg-primary/5 border-primary/30' : '' }}">
                            <input type="checkbox" wire:model="prerequisite_ids" value="{{ $prereq->id }}"
                                class="rounded border-rule text-primary focus:ring-primary focus:ring-offset-0">
                            <span class="text-sm text-ink">{{ $prereq->title }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Bottom Actions --}}
        <div class="flex items-center justify-between pt-4">
            <a href="{{ $fromAdmin ? route('admin.courses.index') : route('instructor.courses.index') }}" class="text-sm text-ink2 hover:text-ink transition-colors">
                ← Back to Courses
            </a>
            <div class="flex items-center gap-3">
                @if($course)
                    <a href="{{ $fromAdmin ? route('admin.courses.curriculum', $course) : route('instructor.courses.curriculum', $course) }}" class="px-5 py-2.5 border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-bg transition-colors">
                        Edit Curriculum →
                    </a>
                @endif
                <button type="submit" class="px-6 py-2.5 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90 transition-opacity">
                    {{ $course ? 'Save Changes' : 'Create Course' }}
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .ql-container { font-family: inherit; font-size: 14px; }
    .ql-editor { min-height: 150px; }
    .ql-toolbar { border-radius: 8px 8px 0 0; border-color: var(--rule) !important; background: var(--bg); }
    .ql-container { border-radius: 0 0 8px 8px; border-color: var(--rule) !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
@endpush
