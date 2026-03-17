<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_suspended_user_is_logged_out(): void
    {
        $user = User::factory()->create(['suspended_at' => now()]);
        $user->assignRole('student');

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_enrolled_user_cannot_access_learn_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');
        $course = Course::factory()->published()->create();

        $response = $this->actingAs($user)
            ->get(route('learn.show', $course->slug));

        $response->assertRedirect(route('courses.show', $course->slug));
    }

    public function test_enrolled_user_can_access_learn_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');
        $course = Course::factory()->published()->create();
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('learn.show', $course->slug));

        $response->assertOk();
    }

    public function test_expired_enrollment_is_blocked(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');
        $course = Course::factory()->published()->create();
        Enrollment::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('learn.show', $course->slug));

        $response->assertRedirect(route('courses.show', $course->slug));
    }

    public function test_admin_bypasses_enrollment_check(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $course = Course::factory()->published()->create();

        $response = $this->actingAs($admin)
            ->get(route('learn.show', $course->slug));

        $response->assertOk();
    }

    public function test_instructor_bypasses_enrollment_for_own_course(): void
    {
        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');
        $course = Course::factory()->published()->create(['instructor_id' => $instructor->id]);

        $response = $this->actingAs($instructor)
            ->get(route('learn.show', $course->slug));

        $response->assertOk();
    }
}
