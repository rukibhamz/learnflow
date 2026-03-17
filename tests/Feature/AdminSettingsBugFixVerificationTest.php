<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fix-Checking Tests — run on FIXED code.
 *
 * Property 1: Expected Behavior A — checked boolean toggles save as 1.
 * Property 2: Expected Behavior B — "Saved" confirmation appears after submit.
 *
 * EXPECTED OUTCOME: All tests PASS, confirming both bugs are resolved.
 * Requirements: 2.1, 2.2
 */
class AdminSettingsBugFixVerificationTest extends TestCase
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
     * Property 1: Expected Behavior A
     * For each boolean toggle, POSTing with the checkbox checked (value=1) must
     * persist 1 in the database after the fix.
     *
     * @dataProvider booleanToggleProvider
     * _Requirements: 2.1_
     */
    public function test_checked_boolean_toggle_saves_as_1(string $field): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                $field => '1',
            ]);

        $this->assertEquals('1', Setting::get($field),
            "Fix A verified: $field submitted as checked (1) should be stored as 1."
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
     * Property 2: Expected Behavior B
     * After a successful POST + redirect, the response must contain the inline
     * "Saved" confirmation text rendered by the fixed Alpine.js expression.
     *
     * _Requirements: 2.2_
     */
    public function test_saved_confirmation_text_visible_after_submit(): void
    {
        $response = $this->actingAs($this->admin)
            ->followingRedirects()
            ->post(route('admin.settings.update'), ['site_name' => 'Test Site']);

        $response->assertSee('Saved');
    }
}
