<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Preservation Property Tests
 *
 * Validates Requirements: 3.1, 3.2, 3.4
 *
 * These tests verify that unrelated behaviour is UNCHANGED even when the bug
 * condition is active (i.e., the `certificate_templates` table and the
 * `courses.certificate_template_id` column are absent).
 *
 * EXPECTED OUTCOME: All tests PASS on unfixed code — this confirms the baseline
 * behaviour to preserve after the migration is applied.
 *
 * **Validates: Requirements 3.1, 3.2, 3.4**
 */
class CertificateTemplatePreservationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Simulate the bug condition: drop the certificate_templates table and
        // the certificate_template_id column from courses, exactly as they would
        // be absent if the migration had never been run.
        if (Schema::hasColumn('courses', 'certificate_template_id')) {
            Schema::table('courses', function ($table) {
                $table->dropForeign(['certificate_template_id']);
                $table->dropColumn('certificate_template_id');
            });
        }

        if (Schema::hasTable('certificate_templates')) {
            Schema::dropIfExists('certificate_templates');
        }
    }

    // ── Property: Course creation succeeds on unfixed schema ─────────────────

    /**
     * Property-based course creation: creating courses with varied attributes
     * succeeds and all non-certificate_template_id columns round-trip correctly.
     *
     * Runs for 5 different random course attribute sets to cover the property
     * across a range of inputs.
     *
     * **Validates: Requirements 3.2**
     */
    public function test_course_creation_succeeds_and_columns_round_trip_on_unfixed_schema(): void
    {
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');

        // Disable Scout indexing — Meilisearch is not available in the test environment.
        Course::withoutSyncingToSearch(function () use ($instructor) {
            $this->runCourseRoundTripAssertions($instructor);
        });
    }

    private function runCourseRoundTripAssertions(User $instructor): void
    {
        for ($i = 0; $i < 5; $i++) {
            $course = Course::factory()->create(['instructor_id' => $instructor->id]);

            // Course creation must not throw — if we reach here it succeeded.
            $this->assertNotNull($course->id, "Course #{$i} should have been persisted with an id");

            // Reload from DB to verify round-trip.
            $fresh = Course::find($course->id);
            $this->assertNotNull($fresh, "Course #{$i} should be retrievable from the database");

            // All non-certificate_template_id columns must round-trip correctly.
            $this->assertEquals($course->title, $fresh->title, "title must round-trip for course #{$i}");
            $this->assertEquals($course->slug, $fresh->slug, "slug must round-trip for course #{$i}");
            $this->assertEquals($course->description, $fresh->description, "description must round-trip for course #{$i}");
            $this->assertEquals($course->short_description, $fresh->short_description, "short_description must round-trip for course #{$i}");
            $this->assertEquals((string) $course->price, (string) $fresh->price, "price must round-trip for course #{$i}");
            $this->assertEquals($course->level, $fresh->level, "level must round-trip for course #{$i}");
            $this->assertEquals($course->language, $fresh->language, "language must round-trip for course #{$i}");
            $this->assertEquals($course->status, $fresh->status, "status must round-trip for course #{$i}");
            $this->assertEquals($course->instructor_id, $fresh->instructor_id, "instructor_id must round-trip for course #{$i}");
        }
    }

    // ── Property: Other admin routes return HTTP 200 ──────────────────────────

    /**
     * GET /admin/users returns HTTP 200 when the bug condition is active.
     *
     * **Validates: Requirements 3.1**
     */
    public function test_admin_users_route_returns_200_on_unfixed_schema(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertStatus(200);
    }

    /**
     * GET /admin/coupons returns HTTP 200 when the bug condition is active.
     *
     * **Validates: Requirements 3.1**
     */
    public function test_admin_coupons_route_returns_200_on_unfixed_schema(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/coupons');

        $response->assertStatus(200);
    }

    /**
     * GET /admin/courses returns HTTP 200 when the bug condition is active.
     *
     * **Validates: Requirements 3.1**
     */
    public function test_admin_courses_route_returns_200_on_unfixed_schema(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/courses');

        $response->assertStatus(200);
    }

    // ── Unit assertion: Certificate::all() works on unfixed schema ────────────

    /**
     * Certificate::all() executes without error and returns a Collection
     * (even if empty) when the bug condition is active.
     *
     * **Validates: Requirements 3.4**
     */
    public function test_certificate_all_returns_collection_on_unfixed_schema(): void
    {
        $result = Certificate::all();

        $this->assertInstanceOf(Collection::class, $result);
    }
}
