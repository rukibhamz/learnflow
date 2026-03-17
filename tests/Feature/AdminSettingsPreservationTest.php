<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Preservation Tests — verify no regressions after the fix.
 *
 * Property 2: Preservation — unchecked toggles still save as 0, non-boolean
 * fields still save their submitted values, test-email form is independent.
 *
 * EXPECTED OUTCOME: All tests PASS on both unfixed and fixed code.
 * Requirements: 3.1, 3.2, 3.4
 */
class AdminSettingsPreservationTest extends TestCase
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
     * Preservation: unchecked boolean toggles must still save as 0.
     * POSTing without the toggle field (simulating unchecked) should persist 0.
     *
     * @dataProvider booleanToggleProvider
     * _Requirements: 3.1_
     */
    public function test_unchecked_boolean_toggle_saves_as_0(string $field): void
    {
        // Pre-seed a 1 so we can confirm it gets overwritten to 0
        Setting::set($field, '1');

        // POST without the toggle field — the hidden fallback value="0" is submitted
        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                $field => '0',
            ]);

        $this->assertEquals('0', Setting::get($field),
            "Preservation: $field submitted as unchecked (0) should remain 0."
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
     * Preservation: non-boolean fields save their submitted string values unchanged.
     *
     * @dataProvider nonBooleanFieldProvider
     * _Requirements: 3.2_
     */
    public function test_non_boolean_field_saves_submitted_value(string $field, string $value): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                $field => $value,
            ]);

        $this->assertEquals($value, Setting::get($field),
            "Preservation: $field should be stored as submitted value '$value'."
        );
    }

    public static function nonBooleanFieldProvider(): array
    {
        return [
            'site_name'              => ['site_name', 'My Academy'],
            'support_email'          => ['support_email', 'help@example.com'],
            'timezone'               => ['timezone', 'America/New_York'],
            'mail_mailer'            => ['mail_mailer', 'smtp'],
            'stripe_publishable_key' => ['stripe_publishable_key', 'pk_test_abc123'],
        ];
    }

    /**
     * Preservation: the test-email form submission is independent of the main
     * settings form and does not alter any settings in the database.
     *
     * _Requirements: 3.4_
     */
    public function test_test_email_form_does_not_affect_settings(): void
    {
        Setting::set('site_name', 'Original Name');

        // POST to the test-email endpoint (will fail mail delivery in test env, but that's fine)
        $this->actingAs($this->admin)
            ->post(route('admin.settings.test-email'), [
                'test_email' => 'test@example.com',
            ]);

        // The main settings value must be untouched
        $this->assertEquals('Original Name', Setting::get('site_name'));
    }
}
