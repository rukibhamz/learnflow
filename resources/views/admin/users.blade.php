@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<div class="space-y-8">
    {{-- Search & Filters --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative w-full max-w-sm">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" placeholder="Search by name or email..." class="w-full h-9 pl-10 pr-4 bg-surface border border-rule rounded-card font-body text-[13px] text-ink focus:outline-none focus:border-accent">
        </div>
        
        <div class="flex items-center gap-4">
            <div class="relative">
                <select class="h-9 pl-4 pr-10 bg-surface border border-rule rounded-card font-body text-[13px] text-ink2 appearance-none focus:outline-none focus:border-accent">
                    <option>All Roles</option>
                    <option>Students</option>
                    <option>Instructors</option>
                    <option>Admins</option>
                </select>
                <span class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-ink3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </span>
            </div>
            <button class="px-4 py-1.5 bg-ink text-white font-display font-bold text-[12px] rounded-card hover:opacity-90 transition-opacity">Add User</button>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-surface border border-rule rounded-card overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="bg-bg border-b border-rule">
                    <th class="text-left py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">User</th>
                    <th class="text-left py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Role</th>
                    <th class="text-left py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Status</th>
                    <th class="text-left py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Joined</th>
                    <th class="text-right py-4 px-6 font-display font-bold text-[10px] uppercase tracking-widest text-ink3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-rule">
                @foreach([
                    ['name' => 'Alex Rivera', 'email' => 'alex@example.com', 'role' => 'Instructor', 'status' => 'Active'],
                    ['name' => 'Sarah Connor', 'email' => 'sarah@example.com', 'role' => 'Student', 'status' => 'Active'],
                    ['name' => 'Mike Wazowski', 'email' => 'mike@example.com', 'role' => 'Student', 'status' => 'Pending'],
                    ['name' => 'Jane Doe', 'email' => 'jane@example.com', 'role' => 'Admin', 'status' => 'Active'],
                ] as $u)
                <tr class="hover:bg-bg transition-colors">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-accent-bg flex items-center justify-center font-display font-bold text-accent text-[11px] uppercase">
                                {{ substr($u['name'], 0, 1) }}
                            </div>
                            <div>
                                <p class="text-[13px] font-bold text-ink leading-none">{{ $u['name'] }}</p>
                                <p class="text-[11px] text-ink3 mt-1">{{ $u['email'] }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-[12px] font-body text-ink2">{{ $u['role'] }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <x-status-badge :status="$u['status']" />
                    </td>
                    <td class="py-4 px-6 text-[12px] text-ink3 font-body">Mar 12, 2026</td>
                    <td class="py-4 px-6 text-right">
                        <button class="text-[11px] font-bold uppercase tracking-widest text-accent hover:underline mr-4">Edit</button>
                        <button class="text-[11px] font-bold uppercase tracking-widest text-ink3 hover:text-warn transition-colors">Deactivate</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
