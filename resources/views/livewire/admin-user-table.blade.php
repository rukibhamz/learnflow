<div>
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex gap-4 mb-8">
        <div class="relative flex-1 max-w-md">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink3">search</span>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search users by name, email, or username..." 
                class="w-full h-11 bg-surface border border-rule rounded-lg pl-10 pr-4 font-body text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all">
        </div>
        <select wire:model.live="roleFilter" class="h-11 px-4 bg-surface border border-rule rounded-lg font-body text-sm text-ink2 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="instructor">Instructor</option>
            <option value="student">Student</option>
        </select>
        <button wire:click="openCreateModal" class="h-11 px-5 bg-primary text-white font-poppins font-bold text-sm rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Add User
        </button>
    </div>

    <div class="bg-surface border border-rule rounded-none overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-background-light text-[11px] font-poppins font-bold uppercase tracking-widest text-ink3 border-b border-rule">
                <tr>
                    <th class="px-6 h-[44px]">User</th>
                    <th class="px-6 h-[44px]">Email</th>
                    <th class="px-6 h-[44px]">Role</th>
                    <th class="px-6 h-[44px] text-center">Enrollments</th>
                    <th class="px-6 h-[44px] text-right">Joined</th>
                    <th class="px-6 h-[44px] text-center">Status</th>
                    <th class="px-6 h-[44px] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-[13px] font-body">
                @forelse($users as $user)
                <tr class="border-b border-rule last:border-0 hover:bg-background-light/30 transition-colors {{ $user->isSuspended() ? 'opacity-60' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                                <span class="text-primary font-bold text-[11px]">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-ink block">{{ $user->name }}</span>
                                <span class="text-[11px] text-ink3">@{{ $user->username }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-ink2">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        @php $role = $user->roles->first()?->name ?? 'none'; @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider 
                            {{ $role === 'admin' ? 'bg-purple-50 text-purple-600 border border-purple-100' : '' }}
                            {{ $role === 'instructor' ? 'bg-blue-50 text-blue-600 border border-blue-100' : '' }}
                            {{ $role === 'student' ? 'bg-gray-50 text-gray-600 border border-gray-200' : '' }}">
                            {{ ucfirst($role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-ink2">{{ $user->enrollments_count }}</td>
                    <td class="px-6 py-4 text-right text-ink3">{{ $user->created_at->format('M j, Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $user->isSuspended() ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-green-50 text-green-600 border border-green-100' }}">
                            {{ $user->isSuspended() ? 'Suspended' : 'Active' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button wire:click="editUser({{ $user->id }})" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-primary transition-colors" title="Edit User">
                                <span class="material-symbols-outlined text-[18px]">edit_square</span>
                            </button>
                            @if(auth()->user()->hasRole('admin'))
                            <button wire:click="openPasswordModal({{ $user->id }})" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-blue-500 transition-colors" title="Manage Password">
                                <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                            </button>
                            @endif
                            <button wire:click="toggleSuspension({{ $user->id }})" wire:confirm="Are you sure you want to {{ $user->isSuspended() ? 'reactivate' : 'suspend' }} this user?" class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-amber-500 transition-colors" title="{{ $user->isSuspended() ? 'Reactivate' : 'Suspend' }}">
                                <span class="material-symbols-outlined text-[18px]">{{ $user->isSuspended() ? 'check_circle' : 'block' }}</span>
                            </button>
                            <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Are you sure you want to delete this user? This action cannot be undone." class="p-1.5 hover:bg-background-light rounded-lg text-ink3 hover:text-red-500 transition-colors" title="Delete User">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-ink3">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>

    {{-- Create User Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="$set('showCreateModal', false)">
        <div class="bg-surface rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="font-poppins font-bold text-lg text-ink mb-6">Create New User</h3>
            <form wire:submit="createUser" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Full Name</label>
                    <input type="text" wire:model="newName" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" required>
                    @error('newName') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Username</label>
                    <input type="text" wire:model="newUsername" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" required>
                    @error('newUsername') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Email</label>
                    <input type="email" wire:model="newEmail" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" required>
                    @error('newEmail') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Password</label>
                    <input type="password" wire:model="newPassword" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" required>
                    @error('newPassword') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Role</label>
                    <select wire:model="newRole" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('newRole') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" wire:click="$set('showCreateModal', false)" class="px-5 py-2.5 border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-bg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:opacity-90 transition-opacity">Create User</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Edit User Modal --}}
    @if($showEditModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="$set('showEditModal', false)">
        <div class="bg-surface rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="font-poppins font-bold text-lg text-ink mb-6">Edit User</h3>
            <form wire:submit="updateUser" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Full Name</label>
                    <input type="text" wire:model="editName" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" required>
                    @error('editName') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Username</label>
                    <input type="text" wire:model="editUsername" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" required>
                    @error('editUsername') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Email</label>
                    <input type="email" wire:model="editEmail" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary" required>
                    @error('editEmail') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">Role</label>
                    <select wire:model="editRole" class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                        <option value="student">Student</option>
                        <option value="instructor">Instructor</option>
                        <option value="admin">Admin</option>
                    </select>
                    @error('editRole') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" wire:click="$set('showEditModal', false)" class="px-5 py-2.5 border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-bg transition-colors">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-bold hover:opacity-90 transition-opacity">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Password Management Modal --}}
    @if($showPasswordModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="closePasswordModal">
        <div class="bg-surface rounded-lg shadow-xl w-full max-w-md p-6">
            <h3 class="font-poppins font-bold text-lg text-ink mb-2">Manage Password</h3>
            <p class="text-sm text-ink3 mb-6">Update the password for this user or send them a reset link.</p>

            {{-- Read-only user context --}}
            <div class="bg-background-light rounded-lg px-4 py-3 mb-6 space-y-1">
                <div class="text-[11px] font-bold uppercase tracking-widest text-ink3">User</div>
                <div class="text-sm font-medium text-ink">{{ $passwordUserName }}</div>
                <div class="text-sm text-ink2">{{ $passwordUserEmail }}</div>
            </div>

            {{-- Direct password set --}}
            <form wire:submit="setPassword" class="space-y-4">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-ink3 mb-2">New Password</label>
                    <input type="password" wire:model="newPasswordValue" placeholder="Min. 8 characters"
                        class="w-full h-11 bg-bg border border-rule rounded-lg px-4 text-sm focus:outline-none focus:border-primary">
                    @error('newPasswordValue') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col gap-3 pt-2">
                    <button type="submit" class="w-full h-11 bg-primary text-white rounded-lg text-sm font-bold hover:opacity-90 transition-opacity">
                        Set Password
                    </button>
                    <button type="button" wire:click="sendResetEmail" class="w-full h-11 border border-rule rounded-lg text-sm font-medium text-ink2 hover:bg-bg transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">mail</span>
                        Send Reset Email
                    </button>
                    <button type="button" wire:click="closePasswordModal" class="w-full h-11 text-sm text-ink3 hover:text-ink transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
