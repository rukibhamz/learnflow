<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.login');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.post'), [
            'login' => $user->email,
            'password' => 'password',
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();
    }

    public function test_users_can_authenticate_with_username(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.post'), [
            'login' => $user->username,
            'password' => 'password',
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.post'), [
            'login' => $user->email,
            'password' => 'wrong-password',
            '_token' => csrf_token(),
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_navigation_menu_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response
            ->assertOk()
            ->assertSeeVolt('layout.navigation');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'), [
            '_token' => csrf_token(),
        ]);

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
