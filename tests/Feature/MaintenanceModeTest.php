<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Property-based tests for the Maintenance Mode feature.
 *
 * Each test iterates over all relevant input combinations to verify
 * correctness properties defined in the design document.
 */
class MaintenanceModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    /**
     * Helper: set maintenance mode in both DB and config (middleware reads config).
     */
    private function setMaintenance(string $value): void
    {
        Setting::set('maintenance_mode', $value);
        config(['settings.maintenance_mode' => $value]);
    }

    /**
     * Helper: return the four affected URLs, creating a published course for the slug route.
     */
    private function affectedUrls(): array
    {
        $course = Course::factory()->published()->create();

        return [
            '/courses',
            '/courses/' . $course->slug,
            '/mentors',
            '/pricing',
        ];
    }

    // -------------------------------------------------------------------------
    // Property 1: Maintenance flag controls overlay visibility for non-exempt users
    // Feature: maintenance-mode, Property 1: Maintenance flag controls overlay visibility for non-exempt users
    // Validates: Requirements 1.1, 1.2, 2.2, 2.3
    // -------------------------------------------------------------------------

    /**
     * @dataProvider property1Provider
     */
    public function test_property1_maintenance_flag_controls_overlay_for_non_exempt_users(
        string $url,
        string $userType,
        string $maintenanceValue
    ): void {
        // Feature: maintenance-mode, Property 1: Maintenance flag controls overlay visibility for non-exempt users
        $this->setMaintenance($maintenanceValue);

        if ($userType === 'guest') {
            $response = $this->get($url);
        } else {
            // student
            $user = User::factory()->create();
            $user->assignRole('student');
            $response = $this->actingAs($user)->get($url);
        }

        if ($maintenanceValue === '1') {
            $response->assertSee('Coming Soon');
        } else {
            $response->assertDontSee('Coming Soon');
        }
    }

    public static function property1Provider(): array
    {
        // We build the dataset statically; the course slug URL is handled
        // by using a placeholder that gets resolved in setUp via affectedUrls().
        // Instead, we use a fixed set of URLs and create the course in the test.
        $userTypes = ['guest', 'student'];
        $maintenanceValues = ['0', '1'];
        $urls = ['/courses', '/mentors', '/pricing'];

        $cases = [];
        foreach ($urls as $url) {
            foreach ($userTypes as $userType) {
                foreach ($maintenanceValues as $value) {
                    $cases["{$url} | {$userType} | maintenance={$value}"] = [$url, $userType, $value];
                }
            }
        }

        return $cases;
    }

    /**
     * Property 1 — also covers /courses/{slug} (requires DB-created course).
     */
    public function test_property1_course_slug_url_for_non_exempt_users(): void
    {
        // Feature: maintenance-mode, Property 1: Maintenance flag controls overlay visibility for non-exempt users
        $course = Course::factory()->published()->create();
        $url = '/courses/' . $course->slug;

        $userTypes = ['guest', 'student'];
        $maintenanceValues = ['0', '1'];

        foreach ($userTypes as $userType) {
            foreach ($maintenanceValues as $maintenanceValue) {
                $this->setMaintenance($maintenanceValue);

                if ($userType === 'guest') {
                    $response = $this->get($url);
                } else {
                    $user = User::factory()->create();
                    $user->assignRole('student');
                    $response = $this->actingAs($user)->get($url);
                }

                if ($maintenanceValue === '1') {
                    $response->assertSee('Coming Soon',
                        "Expected overlay for {$userType} on {$url} with maintenance=1");
                } else {
                    $response->assertDontSee('Coming Soon',
                        "Expected no overlay for {$userType} on {$url} with maintenance=0");
                }
            }
        }
    }

    // -------------------------------------------------------------------------
    // Property 2: Exempt users always see normal content
    // Feature: maintenance-mode, Property 2: Exempt users always see normal content
    // Validates: Requirements 2.1
    // -------------------------------------------------------------------------

    /**
     * @dataProvider property2Provider
     */
    public function test_property2_exempt_users_always_see_normal_content(
        string $userRole,
        string $maintenanceValue
    ): void {
        // Feature: maintenance-mode, Property 2: Exempt users always see normal content
        $this->setMaintenance($maintenanceValue);

        $user = User::factory()->create();
        $user->assignRole($userRole);

        $course = Course::factory()->published()->create();
        $urls = [
            '/courses',
            '/courses/' . $course->slug,
            '/mentors',
            '/pricing',
        ];

        foreach ($urls as $url) {
            $response = $this->actingAs($user)->get($url);
            $response->assertDontSee('Coming Soon',
                "Exempt user ({$userRole}) should never see overlay on {$url} with maintenance={$maintenanceValue}");
        }
    }

    public static function property2Provider(): array
    {
        $roles = ['admin', 'instructor'];
        $maintenanceValues = ['0', '1'];

        $cases = [];
        foreach ($roles as $role) {
            foreach ($maintenanceValues as $value) {
                $cases["{$role} | maintenance={$value}"] = [$role, $value];
            }
        }

        return $cases;
    }

    // -------------------------------------------------------------------------
    // Property 3: All four affected pages show the overlay
    // Feature: maintenance-mode, Property 3: All four affected pages show the overlay
    // Validates: Requirements 3.1, 3.2, 3.3, 3.4
    // -------------------------------------------------------------------------

    public function test_property3_all_four_affected_pages_show_overlay(): void
    {
        // Feature: maintenance-mode, Property 3: All four affected pages show the overlay
        $this->setMaintenance('1');

        $course = Course::factory()->published()->create();
        $urls = [
            '/courses',
            '/courses/' . $course->slug,
            '/mentors',
            '/pricing',
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertSee('Coming Soon',
                "Overlay should be present on affected page {$url} when maintenance=1");
        }
    }

    // -------------------------------------------------------------------------
    // Property 4: Non-affected pages never show the overlay
    // Feature: maintenance-mode, Property 4: Non-affected pages never show the overlay
    // Validates: Requirements 3.5
    // -------------------------------------------------------------------------

    /**
     * @dataProvider property4Provider
     */
    public function test_property4_non_affected_pages_never_show_overlay(string $url): void
    {
        // Feature: maintenance-mode, Property 4: Non-affected pages never show the overlay
        $this->setMaintenance('1');

        $response = $this->get($url);
        $response->assertDontSee('Coming Soon',
            "Non-affected page {$url} should never show the overlay");
    }

    public static function property4Provider(): array
    {
        return [
            'home'     => ['/'],
            'blog'     => ['/blog'],
            'login'    => ['/login'],
            'register' => ['/register'],
        ];
    }

    // -------------------------------------------------------------------------
    // Property 5: Overlay content includes site name and maintenance message
    // Feature: maintenance-mode, Property 5: Overlay content includes site name and maintenance message
    // Validates: Requirements 1.3, 5.1, 5.2
    // -------------------------------------------------------------------------

    /**
     * @dataProvider property5Provider
     */
    public function test_property5_overlay_contains_site_name_and_maintenance_message(string $siteName): void
    {
        // Feature: maintenance-mode, Property 5: Overlay content includes site name and maintenance message
        Setting::set('site_name', $siteName);
        $this->setMaintenance('1');

        $response = $this->get('/courses');

        $response->assertSee($siteName);
        // Assert a maintenance keyword is present
        $response->assertSee('maintenance');
    }

    public static function property5Provider(): array
    {
        return [
            'simple name'         => ['LearnFlow'],
            'name with spaces'    => ['My Academy'],
            'name with numbers'   => ['Academy 2025'],
            'name with ampersand' => ['Tech &amp; Learn'],
            'short name'          => ['LMS'],
        ];
    }

    // -------------------------------------------------------------------------
    // Property 6: Setting persistence round-trip
    // Feature: maintenance-mode, Property 6: Setting persistence round-trip
    // Validates: Requirements 4.1, 4.2, 4.3
    // -------------------------------------------------------------------------

    /**
     * @dataProvider property6Provider
     */
    public function test_property6_setting_persistence_round_trip(string $value): void
    {
        // Feature: maintenance-mode, Property 6: Setting persistence round-trip
        Setting::set('maintenance_mode', $value);

        // Assert DB round-trip
        $stored = Setting::get('maintenance_mode');
        $this->assertEquals($value, $stored,
            "Setting::get('maintenance_mode') should return '{$value}' after Setting::set('maintenance_mode', '{$value}')");

        // Sync config so middleware reflects the stored value
        config(['settings.maintenance_mode' => $stored]);

        // Assert overlay visibility on an affected page matches the stored value
        $response = $this->get('/courses');

        if ($value === '1') {
            $response->assertSee('Coming Soon',
                "Overlay should be visible when maintenance_mode=1");
        } else {
            $response->assertDontSee('Coming Soon',
                "Overlay should not be visible when maintenance_mode=0");
        }
    }

    public static function property6Provider(): array
    {
        return [
            'maintenance on'  => ['1'],
            'maintenance off' => ['0'],
        ];
    }
}
