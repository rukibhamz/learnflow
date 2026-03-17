<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Bug Condition Exploration Tests — run on UNFIXED code.
 *
 * Property 1: Bug Condition A — checked boolean toggles should save as 1.
 * Property 2: Bug Condition B — "Saved" confirmation should appear after submit.
 *
 * EXPECTED OUTCOME: These tests FAIL on unfixed code, confirming both bugs exist.
 */
class AdminSettingsBugConditionTest extends TestCase
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

    /**
     * Property 1: Bug Condition A
     * For each boolean toggle, POSTing with the checkbox checked (value=1) should
     * persist 1 in the database. On unfixed code this FAILS because the hidden
     * input placed after the checkbox always overwrites the value with 0.
     *
     * @dataProvider booleanToggleProvider
     */
    public function test_checked_boolean_toggle_saves_as_1(string $field): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                $field => '1',
            ]);

        // EXPECTED TO FAIL on unfixed code — DB will contain '0' due to hidden-after-checkbox bug
        $this->assertEquals('1', Setting::get($field),
            "Bug A confirmed: $field was submitted as checked (1) but DB stored 0 — hidden input after checkbox overwrites value."
        );
    }

    public static function booleanToggleProvider(): array
    {
        return [
            'feature_instructor_approvals' => ['feature_instructor_approvals'],
            'feature_gamification'         => ['feature_gamification'],
            'maintenance_mode'             => ['maintenance_mode'],
            'mail_use_ssl'                 => ['mail_use_ssl'],
        ];
    }

    /**
     * Property 2: Bug Condition B
     * After a successful POST + redirect, the response should contain the inline
     * "Saved" confirmation text. On unfixed code this FAILS because x-show uses
     * the PHP session() helper which is undefined in Alpine.js scope.
     */
    public function test_saved_confirmation_text_visible_after_submit(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), ['site_name' => 'Test Site']);

        // Follow the redirect and check the rendered page
        $response->assertRedirect();
        $followed = $this->actingAs($this->admin)->followingRedirects()
            ->post(route('admin.settings.update'), ['site_name' => 'Test Site']);

        // EXPECTED TO FAIL on unfixed code — x-show="session('success')" is invalid Alpine.js
        $followed->assertSee('Saved',
            'Bug B confirmed: "Saved" text absent — x-show="session(\'success\')" is not valid Alpine.js.'
        );
    }
}
