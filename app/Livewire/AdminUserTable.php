<?php

namespace App\Livewire;

use Livewire\Component;

class AdminUserTable extends Component
{
    public $search = '';
    public $roleFilter = '';

    public function render()
    {
        $users = [
            ['name' => 'Jane Doe', 'email' => 'jane@example.com', 'role' => 'admin', 'enrolled' => 5, 'joined' => 'Jan 2025', 'suspended' => false],
            ['name' => 'John Smith', 'email' => 'john@example.com', 'role' => 'instructor', 'enrolled' => 0, 'joined' => 'Feb 2025', 'suspended' => false],
            ['name' => 'Alice User', 'email' => 'alice@example.com', 'role' => 'student', 'enrolled' => 12, 'joined' => 'Mar 2025', 'suspended' => true],
        ];
        return view('livewire.admin-user-table', ['users' => $users]);
    }
}
