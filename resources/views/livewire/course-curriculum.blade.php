<div>
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="font-display font-extrabold text-2xl text-ink">Course Curriculum</h1>
            <p class="text-sm text-ink3 mt-1">{{ $course->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('instructor.courses.edit', $course) }}" class="px-4 py-2 border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-bg transition-colors">
                ← Back to Course
            </a>
            <button type="button" wire:click="openAddSectionModal" class="px-5 py-2 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Add Section
            </button>
        </div>
    </div>

    {{-- Sections List with Sortable --}}
    <div x-data="{
        initSortable() {
            const container = this.$refs.sectionsContainer;
            if (!container) return;
            
            new Sortable(container, {
                animation: 150,
                handle: '.section-handle',
                ghostClass: 'opacity-50',
                onEnd: (evt) => {
                    const ids = [...container.querySelectorAll('[data-section-id]')].map(el => el.dataset.sectionId);
                    $wire.reorderSections(ids);
                }
            });

            container.querySelectorAll('[data-lessons-container]').forEach(lessonsEl => {
                new Sortable(lessonsEl, {
                    group: 'lessons',
                    animation: 150,
                    handle: '.lesson-handle',
                    ghostClass: 'opacity-50',
                    onEnd: (evt) => {
                        const sectionId = evt.to.dataset.sectionId;
                        const ids = [...evt.to.querySelectorAll('[data-lesson-id]')].map(el => el.dataset.lessonId);
                        $wire.reorderLessons(sectionId, ids);
                    }
                });
            });
        }
    }" x-init="$nextTick(() => initSortable())" wire:ignore.self>
        
        <div x-ref="sectionsContainer" class="space-y-4">
            @forelse($sections as $section)
                <div data-section-id="{{ $section->id }}" class="bg-surface border border-rule rounded-lg overflow-hidden">
                    {{-- Section Header --}}
                    <div class="flex items-center gap-3 p-4 bg-bg border-b border-rule">
                        <button type="button" class="section-handle cursor-grab p-1 text-ink3 hover:text-ink transition-colors">
                            <span class="material-symbols-outlined text-[20px]">drag_indicator</span>
                        </button>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-display font-bold text-sm text-ink truncate">{{ $section->title }}</h3>
                            @if($section->description)
                                <p class="text-xs text-ink3 truncate mt-0.5">{{ $section->description }}</p>
                            @endif
                        </div>
                        <span class="text-xs text-ink3 shrink-0">{{ $section->lessons->count() }} lessons</span>
                        <div class="flex items-center gap-1">
                            <button type="button" wire:click="openEditSectionModal({{ $section->id }})" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Edit Section">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <button type="button" wire:click="openAddLessonModal({{ $section->id }})" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-green-500 transition-colors" title="Add Lesson">
                                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                            </button>
                            <button type="button" wire:click="deleteSection({{ $section->id }})" wire:confirm="Delete this section and all its lessons?" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-red-500 transition-colors" title="Delete Section">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </div>

                    {{-- Lessons List --}}
                    <div data-lessons-container data-section-id="{{ $section->id }}" class="divide-y divide-rule">
                        @forelse($section->lessons as $lesson)
                            <div data-lesson-id="{{ $lesson->id }}" class="flex items-center gap-3 px-4 py-3 hover:bg-bg transition-colors">
                                <button type="button" class="lesson-handle cursor-grab p-1 text-ink3 hover:text-ink transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">drag_indicator</span>
                                </button>
                                
                                @php
                                    $typeIcons = [
                                        'video' => 'play_circle',
                                        'text' => 'article',
                                        'pdf' => 'picture_as_pdf',
                                        'embed' => 'code',
                                    ];
                                    $typeColors = [
                                        'video' => 'text-blue-500',
                                        'text' => 'text-green-500',
                                        'pdf' => 'text-red-500',
                                        'embed' => 'text-purple-500',
                                    ];
                                @endphp
                                <span class="material-symbols-outlined text-[20px] {{ $typeColors[$lesson->type->value] ?? 'text-ink3' }}">
                                    {{ $typeIcons[$lesson->type->value] ?? 'description' }}
                                </span>

                                <div class="flex-1 min-w-0">
                                    @if($editingInlineId === $lesson->id)
                                        <div class="flex items-center gap-2">
                                            <input type="text" wire:model="editingInlineTitle" 
                                                wire:keydown.enter="saveInlineEdit"
                                                wire:keydown.escape="cancelInlineEdit"
                                                class="flex-1 h-8 bg-bg border border-primary rounded px-2 text-sm focus:outline-none"
                                                autofocus>
                                            <button type="button" wire:click="saveInlineEdit" class="p-1 text-green-500 hover:bg-green-50 rounded">
                                                <span class="material-symbols-outlined text-[18px]">check</span>
                                            </button>
                                            <button type="button" wire:click="cancelInlineEdit" class="p-1 text-ink3 hover:bg-bg rounded">
                                                <span class="material-symbols-outlined text-[18px]">close</span>
                                            </button>
                                        </div>
                                    @else
                                        <span wire:click="startInlineEdit({{ $lesson->id }})" class="text-sm text-ink cursor-pointer hover:text-primary transition-colors">
                                            {{ $lesson->title }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if($lesson->is_preview)
                                        <span class="px-2 py-0.5 bg-green-50 text-green-600 text-[10px] font-bold uppercase rounded">Preview</span>
                                    @endif
                                    @if($lesson->duration_seconds)
                                        <span class="text-xs text-ink3">{{ gmdate('i:s', $lesson->duration_seconds) }}</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1">
                                    <a href="{{ route('instructor.lessons.quiz', $lesson) }}" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="{{ $lesson->quiz ? 'Edit Quiz' : 'Add Quiz' }}">
                                        <span class="material-symbols-outlined text-[18px]">quiz</span>
                                    </a>
                                    <a href="{{ route('instructor.lessons.edit', $lesson) }}" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Edit Lesson">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>
                                    <button type="button" wire:click="deleteLesson({{ $lesson->id }})" wire:confirm="Delete this lesson?" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-red-500 transition-colors" title="Delete Lesson">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-6 text-center text-sm text-ink3">
                                No lessons yet. <button type="button" wire:click="openAddLessonModal({{ $section->id }})" class="text-primary font-medium">Add your first lesson</button>
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="bg-surface border border-rule rounded-lg p-12 text-center">
                    <span class="material-symbols-outlined text-[64px] text-ink3 mb-4 block">library_books</span>
                    <h3 class="font-display font-bold text-lg text-ink mb-2">No sections yet</h3>
                    <p class="text-sm text-ink3 mb-6">Start building your course curriculum by adding sections and lessons.</p>
                    <button type="button" wire:click="openAddSectionModal" class="px-6 py-3 bg-ink text-white font-display font-bold text-sm rounded-lg hover:opacity-90 transition-opacity inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Add First Section
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Section Modal --}}
    @if($showSectionModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="$set('showSectionModal', false)">
        <div class="bg-surface rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="font-display font-bold text-lg text-ink mb-6">{{ $editingSectionId ? 'Edit Section' : 'Add Section' }}</h3>
            
            <form wire:submit="saveSection" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Section Title *</label>
                    <input type="text" wire:model="sectionTitle" 
                        class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                        placeholder="e.g. Getting Started" required autofocus>
                    @error('sectionTitle') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Description (Optional)</label>
                    <textarea wire:model="sectionDescription" rows="3"
                        class="w-full bg-bg border border-rule rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-primary resize-none" 
                        placeholder="Brief description of this section"></textarea>
                    @error('sectionDescription') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" wire:click="$set('showSectionModal', false)" class="px-5 py-2.5 border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-bg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-ink text-white rounded-lg text-sm font-bold hover:opacity-90 transition-opacity">
                        {{ $editingSectionId ? 'Update Section' : 'Add Section' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Lesson Modal --}}
    @if($showLessonModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="$set('showLessonModal', false)">
        <div class="bg-surface rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="font-display font-bold text-lg text-ink mb-6">Add Lesson</h3>
            
            <form wire:submit="saveLesson" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Lesson Title *</label>
                    <input type="text" wire:model="lessonTitle" 
                        class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" 
                        placeholder="e.g. Introduction to Variables" required autofocus>
                    @error('lessonTitle') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Lesson Type *</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach(['video' => ['play_circle', 'Video'], 'text' => ['article', 'Text'], 'pdf' => ['picture_as_pdf', 'PDF'], 'embed' => ['code', 'Embed']] as $type => $info)
                            <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition-colors {{ $lessonType === $type ? 'border-primary bg-primary/5' : 'border-rule hover:bg-bg' }}">
                                <input type="radio" wire:model="lessonType" value="{{ $type }}" class="sr-only">
                                <span class="material-symbols-outlined text-[24px] {{ $lessonType === $type ? 'text-primary' : 'text-ink3' }}">{{ $info[0] }}</span>
                                <span class="text-sm font-medium {{ $lessonType === $type ? 'text-primary' : 'text-ink2' }}">{{ $info[1] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('lessonType') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" wire:click="$set('showLessonModal', false)" class="px-5 py-2.5 border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-bg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-ink text-white rounded-lg text-sm font-bold hover:opacity-90 transition-opacity">
                        Create & Edit Lesson
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush
