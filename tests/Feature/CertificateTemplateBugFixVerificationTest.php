<?php

namespace Tests\Feature;

use App\Models\CertificateTemplate;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Bug Fix Verification Test
 *
 * Validates Requirements: 2.1, 2.2, 2.3
 *
 * Verifies that after the migration runs, the `certificate_templates` table
 * exists, queries succeed, and GET /admin/certificate-templates returns HTTP 200.
 *
 * Uses RefreshDatabase WITHOUT dropping the table — the migration runs normally
 * so the schema is in the FIXED state.
 */
class CertificateTemplateBugFixVerificationTest extends TestCase
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
     * After migration, certificate_templates table exists.
     *
     * Validates: Requirements 2.1, 2.2
     */
    public function test_certificate_templates_table_exists_after_migration(): void
    {
        $this->assertTrue(
            Schema::hasTable('certificate_templates'),
            'certificate_templates table should exist after migration runs'
        );
    }

    /**
     * After migration, courses.certificate_template_id column exists.
     *
     * Validates: Requirements 2.3, 3.2
     */
    public function test_certificate_template_id_column_exists_on_courses(): void
    {
        $this->assertTrue(
            Schema::hasColumn('courses', 'certificate_template_id'),
            'courses.certificate_template_id column should exist after migration runs'
        );
    }

    /**
     * paginate() returns a LengthAwarePaginator (zero items on empty table).
     *
     * Validates: Requirements 2.2
     */
    public function test_paginate_returns_length_aware_paginator_on_empty_table(): void
    {
        $result = CertificateTemplate::latest()->paginate(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertSame(0, $result->total());
    }

    /**
     * getDefault() returns null when no default template exists.
     *
     * Validates: Requirements 2.3, 3.3
     */
    public function test_get_default_returns_null_on_empty_table(): void
    {
        $result = CertificateTemplate::getDefault();

        $this->assertNull($result);
    }

    /**
     * GET /admin/certificate-templates returns HTTP 200 for admin users.
     *
     * Validates: Requirements 2.1
     */
    public function test_admin_certificate_templates_route_returns_200(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/certificate-templates');

        $response->assertStatus(200);
    }
}
