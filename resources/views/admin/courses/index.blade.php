@extends('layouts.admin')

@section('title', 'Manage Courses')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div class="space-y-1">
            <h2 class="font-syne font-bold text-lg tracking-tight text-ink">All Courses</h2>
            <p class="text-[13px] text-ink2 font-body">Review, manage, and monitor all courses on the platform.</p>
        </div>
        <button class="bg-primary text-white px-5 py-2.5 rounded-lg font-syne font-bold text-sm hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Create New Course
        </button>
    </div>

    <div class="bg-surface border border-rule rounded-none overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-background-light text-[11px] font-syne font-bold uppercase tracking-widest text-ink3 border-b border-rule">
                <tr>
                    <th class="px-6 h-[44px]">Course</th>
                    <th class="px-6 h-[44px]">Instructor</th>
                    <th class="px-6 h-[44px]">Category</th>
                    <th class="px-6 h-[44px] text-center">Enrolments</th>
                    <th class="px-6 h-[44px] text-right">Price</th>
                    <th class="px-6 h-[44px] text-center">Status</th>
                    <th class="px-6 h-[44px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-[13px] font-body">
                @foreach(\App\Models\Course::with('instructor')->latest()->get() as $course)
                <tr class="border-b border-rule last:border-0 hover:bg-background-light/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-8 bg-bg border border-rule rounded-sm flex items-center justify-center overflow-hidden shrink-0">
                                @if($course->thumbnail)
                                    <img src="{{ Storage::url($course->thumbnail) }}" class="w-full h-full object-cover">
                                @else
                                    <span class="material-symbols-outlined text-ink3 text-[18px]">image</span>
                                @endif
                            </div>
                            <div class="overflow-hidden">
                                <p class="font-medium text-ink truncate">{{ $course->title }}</p>
                                <p class="text-[11px] text-ink3 mt-0.5">{{ $course->sections_count ?? 0 }} Sections • {{ $course->lessons_count ?? 0 }} Lessons</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-ink2">{{ $course->instructor->name ?? 'System' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-ink2 capitalize">{{ $course->category ?? 'Uncategorized' }}</span>
                    </td>
                    <td class="px-6 py-4 text-center text-ink2">{{ number_format($course->enrollments_count ?? 0) }}</td>
                    <td class="px-6 py-4 text-right font-medium text-ink">${{ number_format($course->price, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusColors = [
                                'published' => 'bg-green-50 text-green-600 border-green-100',
                                'draft' => 'bg-bg text-ink3 border-rule',
                                'pending' => 'bg-amber-50 text-amber-600 border-amber-100',
                            ];
                            $statusColor = $statusColors[strtolower($course->status)] ?? $statusColors['draft'];
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusColor }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="#" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="View Details">
                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                            </a>
                            <button class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Edit Course">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </button>
                            <button class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-red-500 transition-colors" title="Reject/Archive">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
