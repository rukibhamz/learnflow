<div>
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-4 mb-8 flex-wrap">
        <div class="relative flex-1 min-w-[200px] max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink3">search</span>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search courses..." 
                class="w-full h-11 bg-surface border border-rule rounded-lg pl-10 pr-4 font-body text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all">
        </div>
        <select wire:model.live="statusFilter" class="h-11 px-4 bg-surface border border-rule rounded-lg font-body text-sm text-ink2 focus:outline-none focus:border-primary">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="review">In Review</option>
            <option value="published">Published</option>
            <option value="archived">Archived</option>
        </select>
        <a href="{{ route('instructor.courses.create') }}" class="h-11 px-5 bg-ink text-white font-display font-bold text-[12px] rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Create Course
        </a>
    </div>

    <div class="bg-surface border border-rule rounded-lg overflow-x-auto">
        <table class="w-full min-w-[720px]">
            <thead>
                <tr class="bg-bg border-b border-rule">
                    <th class="text-left py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Course</th>
                    <th class="text-left py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Status</th>
                    <th class="text-center py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Students</th>
                    <th class="text-right py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Price</th>
                    <th class="text-right py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Revenue</th>
                    <th class="text-right py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Created</th>
                    <th class="text-right py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3 whitespace-nowrap">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule">
                @forelse($courses as $course)
                <tr class="hover:bg-bg transition-colors">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                @if($course->getFirstMediaUrl('thumbnail'))
                                    <img src="{{ $course->getFirstMediaUrl('thumbnail', 'thumb') }}" class="w-full h-full object-cover" alt="">
                                @else
                                    <span class="font-display font-bold text-primary text-[14px]">{{ strtoupper(substr($course->title, 0, 2)) }}</span>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-[13px] font-bold text-ink leading-tight truncate">{{ $course->title }}</p>
                                <p class="text-[11px] text-ink3 mt-1">{{ $course->sections_count }} sections · {{ $course->lessons_count }} lessons</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-600 border-gray-200',
                                'review' => 'bg-amber-50 text-amber-600 border-amber-200',
                                'published' => 'bg-green-50 text-green-600 border-green-200',
                                'archived' => 'bg-red-50 text-red-600 border-red-200',
                            ];
                            $statusColor = $statusColors[$course->status->value] ?? $statusColors['draft'];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusColor }}">
                            {{ ucfirst($course->status->value) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-center text-[13px] font-medium text-ink2">{{ number_format($course->enrollments_count) }}</td>
                    <td class="py-4 px-6 text-right text-[13px] font-medium text-ink2">
                        @if($course->price > 0)
                            {{ format_price($course->price) }}
                        @else
                            <span class="text-green-600">Free</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right text-[13px] font-medium text-ink2">{{ format_price($course->orders_sum_amount ?? 0) }}</td>
                    <td class="py-4 px-6 text-right text-[13px] text-ink3">{{ $course->created_at->format('M j, Y') }}</td>
                    <td class="py-4 px-6 whitespace-nowrap">
                        <div class="flex items-center justify-end gap-1">
                            {{-- Preview --}}
                            <a href="{{ route('courses.show', $course->slug) }}" target="_blank" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Preview">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                            {{-- Edit --}}
                            <a href="{{ route('instructor.courses.edit', $course) }}" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            {{-- Curriculum --}}
                            <a href="{{ route('instructor.courses.curriculum', $course) }}" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Curriculum">
                                <span class="material-symbols-outlined text-[18px]">list_alt</span>
                            </a>
                            {{-- Status Actions --}}
                            @if($course->status->value === 'draft')
                                <button wire:click="submitForReview({{ $course->id }})" wire:confirm="Submit this course for admin review?" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-blue-500 transition-colors" title="Submit for Review">
                                    <span class="material-symbols-outlined text-[18px]">send</span>
                                </button>
                            @elseif($course->status->value === 'published')
                                <button wire:click="unpublish({{ $course->id }})" wire:confirm="Unpublish this course?" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-amber-500 transition-colors" title="Unpublish">
                                    <span class="material-symbols-outlined text-[18px]">unpublished</span>
                                </button>
                            @endif
                            {{-- Duplicate --}}
                            <button wire:click="duplicateCourse({{ $course->id }})" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Duplicate">
                                <span class="material-symbols-outlined text-[18px]">content_copy</span>
                            </button>
                            {{-- Delete --}}
                            <button wire:click="confirmDelete({{ $course->id }})" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-red-500 transition-colors" title="Delete">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-ink3">
                        <span class="material-symbols-outlined text-[48px] mb-4 block">school</span>
                        No courses yet. <a href="{{ route('instructor.courses.create') }}" class="text-primary font-bold">Create your first course</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($courses->hasPages())
    <div class="mt-6">
        {{ $courses->links() }}
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="$set('showDeleteModal', false)">
        <div class="bg-surface rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="font-display font-bold text-lg text-ink mb-4">Delete Course</h3>
            <p class="text-sm text-ink2 mb-6">Are you sure you want to delete "<strong>{{ $deletingCourseTitle }}</strong>"? This action cannot be undone.</p>
            <div class="flex justify-end gap-3">
                <button type="button" wire:click="$set('showDeleteModal', false)" class="px-5 py-2.5 border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-bg transition-colors">Cancel</button>
                <button type="button" wire:click="deleteCourse" class="px-5 py-2.5 bg-red-500 text-white rounded-lg text-sm font-bold hover:bg-red-600 transition-colors">Delete Course</button>
            </div>
        </div>
    </div>
    @endif
</div>
