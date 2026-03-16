<?php

namespace Tests\Feature;

use App\Livewire\AdminUserTable;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUserTableTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    // ── Listing & search ──────────────────────────────────────────────────────

    public function test_admin_can_see_user_list(): void
    {
        $user = User::factory()->create(['name' => 'John Doe']);

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->assertSee('John Doe');
    }

    public function test_admin_can_search_users_by_name(): void
    {
        User::factory()->create(['name' => 'Alice Smith']);
        User::factory()->create(['name' => 'Bob Jones']);

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->set('search', 'Alice')
            ->assertSee('Alice Smith')
            ->assertDontSee('Bob Jones');
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        $instructor = User::factory()->create(['name' => 'Instructor User']);
        $instructor->assignRole('instructor');

        $student = User::factory()->create(['name' => 'Student User']);
        $student->assignRole('student');

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->set('roleFilter', 'instructor')
            ->assertSee('Instructor User')
            ->assertDontSee('Student User');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_admin_can_create_a_new_user(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('openCreateModal')
            ->set('newName', 'New Student')
            ->set('newEmail', 'newstudent@example.com')
            ->set('newUsername', 'newstudent')
            ->set('newPassword', 'password123')
            ->set('newRole', 'student')
            ->call('createUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'newstudent@example.com']);

        $user = User::where('email', 'newstudent@example.com')->first();
        $this->assertTrue($user->hasRole('student'));
    }

    public function test_create_user_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('openCreateModal')
            ->set('newName', 'Another User')
            ->set('newEmail', 'taken@example.com')
            ->set('newUsername', 'anotheruser')
            ->set('newPassword', 'password123')
            ->set('newRole', 'student')
            ->call('createUser')
            ->assertHasErrors(['newEmail']);
    }

    public function test_create_user_requires_unique_username(): void
    {
        User::factory()->create(['username' => 'takenuser']);

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('openCreateModal')
            ->set('newName', 'Another User')
            ->set('newEmail', 'another@example.com')
            ->set('newUsername', 'takenuser')
            ->set('newPassword', 'password123')
            ->set('newRole', 'student')
            ->call('createUser')
            ->assertHasErrors(['newUsername']);
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function test_admin_can_edit_a_user(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);
        $user->assignRole('student');

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('editUser', $user->id)
            ->set('editName', 'New Name')
            ->set('editEmail', $user->email)
            ->set('editUsername', $user->username)
            ->set('editRole', 'instructor')
            ->call('updateUser')
            ->assertHasNoErrors();

        $this->assertEquals('New Name', $user->fresh()->name);
        $this->assertTrue($user->fresh()->hasRole('instructor'));
    }

    // ── Suspend / Reactivate ──────────────────────────────────────────────────

    public function test_admin_can_suspend_a_user(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('toggleSuspension', $user->id);

        $this->assertNotNull($user->fresh()->suspended_at);
    }

    public function test_admin_can_reactivate_a_suspended_user(): void
    {
        $user = User::factory()->create(['suspended_at' => now()]);

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('toggleSuspension', $user->id);

        $this->assertNull($user->fresh()->suspended_at);
    }

    public function test_admin_cannot_suspend_themselves(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('toggleSuspension', $this->admin->id);

        // The component flashes an error and does NOT suspend the admin
        $this->assertNull($this->admin->fresh()->suspended_at);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function test_admin_can_delete_a_user(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('deleteUser', $user->id);

        $this->assertNull(User::find($user->id));
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('deleteUser', $this->admin->id);

        // The component flashes an error and does NOT delete the admin
        $this->assertNotNull(User::find($this->admin->id));
    }
}
