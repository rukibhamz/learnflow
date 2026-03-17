@extends('layouts.dashboard')

@section('title', 'My Certificates')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="font-display font-extrabold text-2xl text-ink">Certificates</h1>
            <p class="text-[13px] font-body text-ink2 mt-1">Showcase your verified achievements.</p>
        </div>
        <a href="{{ route('courses.index') }}" class="px-5 py-2.5 bg-accent text-white font-display font-bold text-[12px] rounded-card hover:opacity-90 transition-opacity">Explore Courses</a>
    </div>

    <div x-data="{ copied: false }" class="bg-surface border border-rule rounded-card overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-bg border-b border-rule">
                <tr>
                    <th class="text-left px-5 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Course</th>
                    <th class="text-left px-5 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Issued</th>
                    <th class="text-right px-5 py-3 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule">
                @forelse($certificates as $certificate)
                    <tr>
                        <td class="px-5 py-3">
                            <div class="font-medium text-ink">{{ $certificate->course->title }}</div>
                            <div class="text-[11px] text-ink3 font-mono">{{ strtoupper($certificate->uuid) }}</div>
                        </td>
                        <td class="px-5 py-3 text-ink2">
                            {{ $certificate->issued_at->format('M j, Y') }}
                        </td>
                        <td class="px-5 py-3 text-right space-x-3">
                            <a href="{{ route('certificates.download', $certificate->uuid) }}"
                               class="inline-flex items-center gap-1 text-xs text-primary hover:underline">
                                <span class="material-symbols-outlined text-[16px]">download</span>
                                Download PDF
                            </a>
                            <button type="button"
                                @click="navigator.clipboard.writeText('{{ route('certificates.verify', $certificate->uuid) }}'); copied=true; setTimeout(() => copied=false, 1500)"
                                class="inline-flex items-center gap-1 text-xs text-ink hover:underline">
                                <span class="material-symbols-outlined text-[16px]">link</span>
                                Copy Verify Link
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-5 py-10 text-center text-ink3">
                            You don't have any certificates yet. Complete a course to earn one!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div x-show="copied" x-cloak class="px-5 py-3 text-xs text-green-700 bg-green-50 border-t border-green-200">
            Verify link copied.
        </div>
    </div>
</div>
@endsection
