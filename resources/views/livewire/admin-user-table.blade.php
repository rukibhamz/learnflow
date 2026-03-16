<div>
    <div class="flex gap-4 mb-8">
        <div class="relative flex-1 max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink3">search</span>
            <input type="search" wire:model.live="search" placeholder="Search users by name or email..." 
                class="w-full h-11 bg-surface border border-rule rounded-lg pl-10 pr-4 font-body text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all">
        </div>
        <select wire:model.live="roleFilter" class="h-11 px-4 bg-surface border border-rule rounded-lg font-body text-sm text-ink2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="instructor">Instructor</option>
            <option value="student">Student</option>
        </select>
    </div>

    <div class="bg-surface border border-rule rounded-none overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-background-light text-[11px] font-syne font-bold uppercase tracking-widest text-ink3 border-b border-rule">
                <tr>
                    <th class="px-6 h-[44px]">User</th>
                    <th class="px-6 h-[44px]">Email</th>
                    <th class="px-6 h-[44px]">Role</th>
                    <th class="px-6 h-[44px] text-center">Courses</th>
                    <th class="px-6 h-[44px] text-right">Joined</th>
                    <th class="px-6 h-[44px] text-center">Status</th>
                    <th class="px-6 h-[44px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-[13px] font-body">
                @foreach($users as $user)
                <tr class="border-b border-rule last:border-0 hover:bg-background-light/30 transition-colors {{ $user['suspended'] ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                                <span class="text-primary font-bold text-[11px]">{{ strtoupper(substr($user['name'], 0, 2)) }}</span>
                            </div>
                            <span class="font-medium text-ink">{{ $user['name'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-ink2">{{ $user['email'] }}</td>
                    <td class="px-6 py-4 capitalize text-ink2">{{ $user['role'] }}</td>
                    <td class="px-6 py-4 text-center text-ink2">{{ $user['enrolled'] }}</td>
                    <td class="px-6 py-4 text-right text-ink3">{{ $user['joined'] }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $user['suspended'] ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-green-50 text-green-600 border border-green-100' }}">
                            {{ $user['suspended'] ? 'Suspended' : 'Active' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Edit Role">
                                <span class="material-symbols-outlined text-[18px]">edit_square</span>
                            </button>
                            <button class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-red-500 transition-colors" title="Toggle Suspension">
                                <span class="material-symbols-outlined text-[18px]">block</span>
                            </button>
                            <a href="#" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Impersonate">
                                <span class="material-symbols-outlined text-[18px]">login</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-6">
        {{ $users->links() }}
    </div>
    @endif
</div>
