<div>
    <div class="flex gap-4 mb-6">
        <input type="search" wire:model.live="search" placeholder="Search users..." class="flex-1 max-w-md h-9 px-4 border border-rule rounded-card font-body text-sm bg-surface focus:ring-2 focus:ring-accent">
        <select wire:model.live="roleFilter" class="h-9 px-4 border border-rule rounded-card font-body text-sm bg-surface">
            <option value="">All roles</option>
            <option value="admin">Admin</option>
            <option value="instructor">Instructor</option>
            <option value="student">Student</option>
        </select>
    </div>
    <div class="bg-surface border border-rule rounded-card overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-rule">
                    <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase text-ink3 w-12"> </th>
                    <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase text-ink3">Name</th>
                    <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase text-ink3">Email</th>
                    <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase text-ink3">Role</th>
                    <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase text-ink3">Enrolled</th>
                    <th class="text-left py-3 px-4 font-display font-bold text-[11px] uppercase text-ink3">Joined</th>
                    <th class="text-right py-3 px-4 font-display font-bold text-[11px] uppercase text-ink3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b border-rule {{ $user['suspended'] ? 'opacity-50' : '' }}">
                    <td class="py-3 px-4">
                        <div class="w-9 h-9 rounded-full bg-accent-bg flex items-center justify-center font-display font-bold text-accent text-xs">
                            {{ strtoupper(substr($user['name'], 0, 2)) }}
                        </div>
                    </td>
                    <td class="py-3 px-4 font-body text-[13px]">{{ $user['name'] }}</td>
                    <td class="py-3 px-4 font-body text-[13px] text-ink2">{{ $user['email'] }}</td>
                    <td class="py-3 px-4">
                        @include('components.status-badge', ['status' => $user['role']])
                    </td>
                    <td class="py-3 px-4 font-body text-[13px]">{{ $user['enrolled'] }}</td>
                    <td class="py-3 px-4 font-body text-[13px] text-ink3">{{ $user['joined'] }}</td>
                    <td class="py-3 px-4 text-right">
                        <select class="text-[12px] border border-rule rounded-tag px-2 py-1 bg-surface">
                            <option>Edit role</option>
                        </select>
                        <button class="text-[12px] text-ink3 ml-2">Toggle suspend</button>
                        <a href="#" class="text-accent text-[12px] ml-2 hover:underline">Impersonate</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
