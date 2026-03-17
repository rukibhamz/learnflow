<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiCourseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_can_list_published_courses(): void
    {
        Course::factory()->published()->count(3)->create();
        Course::factory()->create(['status' => CourseStatus::Draft]);

        $response = $this->getJson('/api/courses');

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'title', 'slug', 'price']], 'meta']);
    }

    public function test_can_search_courses(): void
    {
        Course::factory()->published()->create(['title' => 'Laravel Mastery']);
        Course::factory()->published()->create(['title' => 'React Basics']);

        $response = $this->getJson('/api/courses?search=Laravel');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Laravel Mastery');
    }

    public function test_can_view_course_detail(): void
    {
        $course = Course::factory()->published()->create();

        $response = $this->getJson('/api/courses/' . $course->slug);

        $response->assertOk()
            ->assertJsonPath('data.id', $course->id)
            ->assertJsonStructure(['data' => ['id', 'title', 'description', 'curriculum']]);
    }

    public function test_draft_course_is_404(): void
    {
        $course = Course::factory()->create(['status' => CourseStatus::Draft]);

        $response = $this->getJson('/api/courses/' . $course->slug);

        $response->assertNotFound();
    }

    public function test_can_list_user_enrollments(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');
        $course = Course::factory()->published()->create();
        Enrollment::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/enrollments');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_can_enroll_in_free_course_via_api(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');
        $course = Course::factory()->published()->free()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/enrollments', ['course_id' => $course->id]);

        $response->assertCreated()
            ->assertJsonPath('message', 'Enrolled successfully.');

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_cannot_enroll_in_paid_course_via_api(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');
        $course = Course::factory()->published()->create(['price' => 49.99]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/enrollments', ['course_id' => $course->id]);

        $response->assertUnprocessable();
    }
}
