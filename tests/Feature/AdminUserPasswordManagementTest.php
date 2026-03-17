<?php

namespace Tests\Feature;

use App\Livewire\AdminUserTable;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUserPasswordManagementTest extends TestCase
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

    // ── openPasswordModal ─────────────────────────────────────────────────────

    public function test_open_password_modal_populates_user_data(): void
    {
        $user = User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('openPasswordModal', $user->id)
            ->assertSet('showPasswordModal', true)
            ->assertSet('passwordUserName', 'Jane Doe')
            ->assertSet('passwordUserEmail', 'jane@example.com');
    }

    // Property 1: Modal opens with correct target user data
    public function test_property_modal_opens_with_correct_user_data(): void
    {
        // Feature: admin-user-password-management, Property 1: Modal opens with correct target user data
        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();

            $component = Livewire::actingAs($this->admin)
                ->test(AdminUserTable::class)
                ->call('openPasswordModal', $user->id);

            $component->assertSet('showPasswordModal', true);
            $component->assertSet('passwordUserName', $user->name);
            $component->assertSet('passwordUserEmail', $user->email);

            $user->delete();
        }
    }

    // ── closePasswordModal ────────────────────────────────────────────────────

    public function test_close_password_modal_resets_all_fields(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('openPasswordModal', $user->id)
            ->call('closePasswordModal')
            ->assertSet('showPasswordModal', false)
            ->assertSet('passwordUserId', null)
            ->assertSet('passwordUserName', '')
            ->assertSet('passwordUserEmail', '')
            ->assertSet('newPasswordValue', '');
    }

    // ── setPassword ───────────────────────────────────────────────────────────

    public function test_admin_can_set_password_for_user(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('openPasswordModal', $user->id)
            ->set('newPasswordValue', 'newpassword123')
            ->call('setPassword')
            ->assertHasNoErrors()
            ->assertSet('showPasswordModal', false);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_admin_can_set_password_for_own_account(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('openPasswordModal', $this->admin->id)
            ->set('newPasswordValue', 'mynewpassword')
            ->call('setPassword')
            ->assertHasNoErrors()
            ->assertSet('showPasswordModal', false);

        $this->assertTrue(Hash::check('mynewpassword', $this->admin->fresh()->password));
    }

    public function test_set_password_requires_minimum_8_characters(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('openPasswordModal', $user->id)
            ->set('newPasswordValue', 'short')
            ->call('setPassword')
            ->assertHasErrors(['newPasswordValue'])
            ->assertSet('showPasswordModal', true);
    }

    // Property 2: Short passwords are rejected and modal stays open
    public function test_property_short_passwords_rejected(): void
    {
        // Feature: admin-user-password-management, Property 2: Short passwords are rejected and modal stays open
        $user = User::factory()->create();
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';

        for ($i = 0; $i < 100; $i++) {
            $length = rand(0, 7);
            $password = $length === 0 ? '' : substr(str_shuffle(str_repeat($chars, 8)), 0, $length);

            $component = Livewire::actingAs($this->admin)
                ->test(AdminUserTable::class)
                ->call('openPasswordModal', $user->id)
                ->set('newPasswordValue', $password)
                ->call('setPassword');

            $component->assertHasErrors(['newPasswordValue']);
            $component->assertSet('showPasswordModal', true);
        }
    }

    // Property 3: Password set is a hash round-trip
    public function test_property_password_hash_round_trip(): void
    {
        // Feature: admin-user-password-management, Property 3: Password set is a hash round-trip
        $user = User::factory()->create();
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$';

        for ($i = 0; $i < 100; $i++) {
            $length = rand(8, 64);
            $password = substr(str_shuffle(str_repeat($chars, 8)), 0, $length);

            Livewire::actingAs($this->admin)
                ->test(AdminUserTable::class)
                ->call('openPasswordModal', $user->id)
                ->set('newPasswordValue', $password)
                ->call('setPassword')
                ->assertHasNoErrors();

            $this->assertTrue(Hash::check($password, $user->fresh()->password));
        }
    }

    // ── sendResetEmail ────────────────────────────────────────────────────────

    public function test_admin_can_send_password_reset_email(): void
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::RESET_LINK_SENT);

        $user = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('openPasswordModal', $user->id)
            ->call('sendResetEmail')
            ->assertSet('showPasswordModal', false);
    }

    public function test_broker_failure_keeps_modal_open_with_error(): void
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::INVALID_USER);

        $user = User::factory()->create();

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('openPasswordModal', $user->id)
            ->call('sendResetEmail')
            ->assertSet('showPasswordModal', true);
    }

    public function test_reset_email_sent_regardless_of_email_verification(): void
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::RESET_LINK_SENT);

        $user = User::factory()->create(['email_verified_at' => null]);

        Livewire::actingAs($this->admin)
            ->test(AdminUserTable::class)
            ->call('openPasswordModal', $user->id)
            ->call('sendResetEmail')
            ->assertSet('showPasswordModal', false);
    }

    // ── Access control ────────────────────────────────────────────────────────

    public function test_non_admin_cannot_see_password_button(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $target = User::factory()->create();

        $html = Livewire::actingAs($student)
            ->test(AdminUserTable::class)
            ->html();

        $this->assertStringNotContainsString('openPasswordModal', $html);
    }

    public function test_non_admin_set_password_returns_403(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $target = User::factory()->create();

        Livewire::actingAs($student)
            ->test(AdminUserTable::class)
            ->call('openPasswordModal', $target->id)
            ->set('newPasswordValue', 'somepassword')
            ->call('setPassword')
            ->assertStatus(403);
    }

    public function test_non_admin_send_reset_email_returns_403(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');
        $target = User::factory()->create();

        Livewire::actingAs($student)
            ->test(AdminUserTable::class)
            ->set('passwordUserEmail', $target->email)
            ->call('sendResetEmail')
            ->assertStatus(403);
    }

    // Property 4: Non-admin users see no password management controls
    public function test_property_non_admin_no_password_button(): void
    {
        // Feature: admin-user-password-management, Property 4: Non-admin users see no password management controls
        $roles = ['student', 'instructor'];

        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();
            $user->assignRole($roles[array_rand($roles)]);

            $html = Livewire::actingAs($user)
                ->test(AdminUserTable::class)
                ->html();

            $this->assertStringNotContainsString('openPasswordModal', $html);

            $user->delete();
        }
    }

    // Property 5: Non-admin direct invocation returns 403
    public function test_property_non_admin_direct_invocation_403(): void
    {
        // Feature: admin-user-password-management, Property 5: Non-admin direct invocation returns 403
        $roles = ['student', 'instructor'];
        $target = User::factory()->create();

        for ($i = 0; $i < 100; $i++) {
            $user = User::factory()->create();
            $user->assignRole($roles[array_rand($roles)]);

            $setPasswordThrew = false;
            try {
                Livewire::actingAs($user)
                    ->test(AdminUserTable::class)
                    ->call('openPasswordModal', $target->id)
                    ->set('newPasswordValue', 'somepassword123')
                    ->call('setPassword')
                    ->assertStatus(403);
                $setPasswordThrew = true;
            } catch (\Throwable $e) {
                $setPasswordThrew = false;
            }
            $this->assertTrue($setPasswordThrew, "setPassword() should abort 403 for non-admin");

            $sendResetThrew = false;
            try {
                Livewire::actingAs($user)
                    ->test(AdminUserTable::class)
                    ->set('passwordUserEmail', $target->email)
                    ->call('sendResetEmail')
                    ->assertStatus(403);
                $sendResetThrew = true;
            } catch (\Throwable $e) {
                $sendResetThrew = false;
            }
            $this->assertTrue($sendResetThrew, "sendResetEmail() should abort 403 for non-admin");

            $user->delete();
        }
    }
}
