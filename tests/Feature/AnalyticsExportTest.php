<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_revenue_export_returns_csv()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $course = Course::factory()->create();
        Order::create([
            'user_id' => User::factory()->create()->id,
            'course_id' => $course->id,
            'amount' => 5000,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.analytics.export', ['type' => 'revenue']));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
        $response->assertHeader('content-disposition');
    }

    public function test_enrollment_export_returns_csv()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $student = User::factory()->create();
        $student->assignRole('student');
        $course = Course::factory()->create();
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.analytics.export', ['type' => 'enrollments']));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
    }

    public function test_invalid_export_type_returns_404()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.analytics.export', ['type' => 'invalid']));

        $response->assertNotFound();
    }

    public function test_non_admin_cannot_export()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student)->get(route('admin.analytics.export', ['type' => 'revenue']));

        $response->assertForbidden();
    }
}
