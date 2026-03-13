@extends('layouts.dashboard')

@section('title', 'My Courses')

@prepend('sidebar')
    @php
        $instructorNav = [
            ['label' => 'Overview', 'url' => url('/instructor/dashboard'), 'match' => 'instructor/dashboard'],
            ['label' => 'Courses', 'url' => url('/instructor/courses'), 'match' => 'instructor/courses*'],
            ['label' => 'Payments', 'url' => '#', 'match' => 'instructor/payments*'],
            ['label' => 'Settings', 'url' => '#', 'match' => 'instructor/settings*'],
        ];
    @endphp

    @foreach($instructorNav as $item)
        <a href="{{ $item['url'] }}" 
           class="flex items-center px-4 py-2.5 text-[13px] font-medium transition-all duration-150 {{ request()->is($item['match']) ? 'bg-accent-bg text-accent border-r-2 border-accent' : 'text-ink2 hover:bg-bg hover:text-ink' }}">
            {{ $item['label'] }}
        </a>
    @endforeach
@endprepend

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="font-display font-extrabold text-2xl text-ink">Manage Courses</h1>
            <p class="text-[13px] font-body text-ink2 mt-1">Review and update your published content.</p>
        </div>
        <a href="{{ url('/instructor/courses/create') }}" class="px-5 py-2.5 bg-ink text-white font-display font-bold text-[12px] rounded-card hover:opacity-90 transition-opacity">Create Course</a>
    </div>

    <div class="bg-surface border border-rule rounded-card overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-bg border-b border-rule">
                    <th class="text-left py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Course</th>
                    <th class="text-left py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Status</th>
                    <th class="text-left py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Students</th>
                    <th class="text-left py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Revenue</th>
                    <th class="text-right py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule">
                @foreach($courses as $course)
                <tr class="hover:bg-bg transition-colors">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-accent rounded-card flex items-center justify-center font-display font-bold text-white text-[12px]">
                                {{ strtoupper(substr($course['title'], 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-[13px] font-bold text-ink leading-tight">{{ $course['title'] }}</p>
                                <p class="text-[11px] text-ink3 mt-1">Last updated {{ $course['updated'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm italic font-body">
                         <x-status-badge :status="$course['status']" />
                    </td>
                    <td class="py-4 px-6 text-[13px] font-medium text-ink2">{{ $course['students'] }}</td>
                    <td class="py-4 px-6 text-[13px] font-medium text-ink2">${{ number_format($course['students'] * $course['price'], 2) }}</td>
                    <td class="py-4 px-6 text-right">
                        <a href="{{ url('/instructor/courses/1/edit') }}" class="text-[11px] font-bold uppercase tracking-widest text-accent hover:underline">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
