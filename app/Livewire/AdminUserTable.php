<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class AdminUserTable extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';

    public $showEditModal = false;
    public $editingUserId = null;
    public $editName = '';
    public $editEmail = '';
    public $editUsername = '';
    public $editRole = '';

    public $showCreateModal = false;
    public $newName = '';
    public $newEmail = '';
    public $newUsername = '';
    public $newPassword = '';
    public $newRole = 'student';

    public $showPasswordModal = false;
    public $passwordUserId = null;
    public $passwordUserName = '';
    public $passwordUserEmail = '';
    public $newPasswordValue = '';

    protected $queryString = ['search', 'roleFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['newName', 'newEmail', 'newUsername', 'newPassword', 'newRole']);
        $this->newRole = 'student';
        $this->showCreateModal = true;
    }

    public function createUser()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
            'newEmail' => 'required|email|unique:users,email',
            'newUsername' => 'required|string|max:30|min:3|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'newPassword' => 'required|string|min:8',
            'newRole' => 'required|in:admin,instructor,student',
        ]);

        $user = User::create([
            'name' => $this->newName,
            'email' => $this->newEmail,
            'username' => $this->newUsername,
            'password' => bcrypt($this->newPassword),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($this->newRole);

        $this->showCreateModal = false;
        session()->flash('success', 'User created successfully.');
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->editingUserId = $userId;
        $this->editName = $user->name;
        $this->editEmail = $user->email;
        $this->editUsername = $user->username;
        $this->editRole = $user->roles->first()?->name ?? 'student';
        $this->showEditModal = true;
    }

    public function updateUser()
    {
        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|unique:users,email,' . $this->editingUserId,
            'editUsername' => 'required|string|max:30|min:3|unique:users,username,' . $this->editingUserId . '|regex:/^[a-zA-Z0-9_]+$/',
            'editRole' => 'required|in:admin,instructor,student',
        ]);

        $user = User::findOrFail($this->editingUserId);
        $user->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
            'username' => $this->editUsername,
        ]);

        $user->syncRoles([$this->editRole]);

        $this->showEditModal = false;
        session()->flash('success', 'User updated successfully.');
    }

    public function toggleSuspension($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot suspend yourself.');
            return;
        }

        $user->suspended_at = $user->suspended_at ? null : now();
        $user->save();

        session()->flash('success', $user->suspended_at ? 'User suspended.' : 'User reactivated.');
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            session()->flash('error', 'You cannot delete yourself.');
            return;
        }

        $user->delete();
        session()->flash('success', 'User deleted successfully.');
    }

    public function openPasswordModal($userId): void
    {
        $user = User::findOrFail($userId);
        $this->passwordUserId = $user->id;
        $this->passwordUserName = $user->name;
        $this->passwordUserEmail = $user->email;
        $this->newPasswordValue = '';
        $this->showPasswordModal = true;
    }

    public function closePasswordModal(): void
    {
        $this->showPasswordModal = false;
        $this->passwordUserId = null;
        $this->passwordUserName = '';
        $this->passwordUserEmail = '';
        $this->newPasswordValue = '';
    }

    public function setPassword(): void
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        $this->validate([
            'newPasswordValue' => 'required|string|min:8',
        ]);

        $user = User::findOrFail($this->passwordUserId);
        $user->update(['password' => Hash::make($this->newPasswordValue)]);

        $this->closePasswordModal();
        session()->flash('success', 'Password updated successfully.');
    }

    public function sendResetEmail(): void
    {
        abort_unless(auth()->user()->hasRole('admin'), 403);

        $status = Password::sendResetLink(['email' => $this->passwordUserEmail]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->closePasswordModal();
            session()->flash('success', 'Password reset email sent.');
        } else {
            session()->flash('error', 'Failed to send password reset email.');
        }
    }

    public function render()
    {
        $query = User::with('roles')->withCount('enrollments');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->roleFilter) {
            $query->whereHas('roles', fn($q) => $q->where('name', $this->roleFilter));
        }

        $users = $query->latest()->paginate(15);
        $roles = Role::all();

        return view('livewire.admin-user-table', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
