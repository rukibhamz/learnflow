<?php

namespace Tests\Feature;

use App\Models\CertificateTemplate;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Bug Condition Exploration Test
 *
 * Validates Requirements: 1.1, 1.2, 1.3
 *
 * This test simulates the bug condition where the `certificate_templates` table
 * and the `courses.certificate_template_id` column do NOT exist.
 *
 * EXPECTED OUTCOME: This test is expected to FAIL on unfixed code.
 * Failure confirms the bug exists (SQLSTATE[42S02] / MySQL error 1146).
 *
 * The test uses RefreshDatabase (which runs all migrations), then manually
 * drops the table and column to reproduce the exact bug condition.
 */
class CertificateTemplateBugConditionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Simulate the bug condition: drop the certificate_templates table
        // and the certificate_template_id column from courses, exactly as they
        // would be absent if the migration had never been run.
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

    /**
     * isBugCondition: certificate_templates table is absent.
     *
     * Validates: Requirements 1.1, 1.2, 1.3
     */
    public function test_bug_condition_certificate_templates_table_does_not_exist(): void
    {
        $this->assertFalse(
            Schema::hasTable('certificate_templates'),
            'isBugCondition: certificate_templates table should NOT exist in the bug state'
        );
    }

    /**
     * isBugCondition: courses.certificate_template_id column is absent.
     *
     * Validates: Requirements 1.2, 1.3
     */
    public function test_bug_condition_certificate_template_id_column_does_not_exist(): void
    {
        $this->assertFalse(
            Schema::hasColumn('courses', 'certificate_template_id'),
            'isBugCondition: courses.certificate_template_id column should NOT exist in the bug state'
        );
    }

    /**
     * Querying the missing table via paginate() throws QueryException (SQLSTATE 42S02).
     *
     * Validates: Requirements 1.2
     */
    public function test_paginate_throws_query_exception_when_table_missing(): void
    {
        $this->expectException(QueryException::class);

        CertificateTemplate::latest()->paginate(10);
    }

    /**
     * Calling getDefault() throws QueryException when the table is missing.
     *
     * Validates: Requirements 1.3
     */
    public function test_get_default_throws_query_exception_when_table_missing(): void
    {
        $this->expectException(QueryException::class);

        CertificateTemplate::getDefault();
    }

    /**
     * GET /admin/certificate-templates returns 500 (not 200) when the table is missing.
     *
     * Validates: Requirements 1.1
     */
    public function test_admin_certificate_templates_route_returns_500_when_table_missing(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/certificate-templates');

        $response->assertStatus(500);
    }
}
