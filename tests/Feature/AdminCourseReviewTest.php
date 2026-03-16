<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use App\Livewire\AdminCourseReview;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminCourseReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $instructor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->instructor = User::factory()->create();
        $this->instructor->assignRole('instructor');
    }

    public function test_admin_can_see_courses_in_review(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'status' => CourseStatus::Review,
            'title' => 'Pending Review Course',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminCourseReview::class)
            ->assertSee('Pending Review Course');
    }

    public function test_admin_does_not_see_draft_courses_in_review_queue(): void
    {
        $draft = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'status' => CourseStatus::Draft,
            'title' => 'Draft Course',
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminCourseReview::class)
            ->assertDontSee('Draft Course');
    }

    public function test_admin_can_approve_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'status' => CourseStatus::Review,
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminCourseReview::class)
            ->call('approveCourse', $course->id);

        $this->assertEquals(CourseStatus::Published, $course->fresh()->status);
    }

    public function test_admin_can_reject_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'status' => CourseStatus::Review,
        ]);

        Livewire::actingAs($this->admin)
            ->test(AdminCourseReview::class)
            ->call('rejectCourse', $course->id);

        $this->assertEquals(CourseStatus::Draft, $course->fresh()->status);
    }

    public function test_admin_can_search_review_queue_by_title(): void
    {
        Course::factory()->create(['instructor_id' => $this->instructor->id, 'status' => CourseStatus::Review, 'title' => 'Python for Beginners']);
        Course::factory()->create(['instructor_id' => $this->instructor->id, 'status' => CourseStatus::Review, 'title' => 'Advanced JavaScript']);

        Livewire::actingAs($this->admin)
            ->test(AdminCourseReview::class)
            ->set('search', 'Python')
            ->assertSee('Python for Beginners')
            ->assertDontSee('Advanced JavaScript');
    }

    public function test_non_admin_cannot_approve_course(): void
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'status' => CourseStatus::Review,
        ]);

        // Livewire wraps authorization exceptions; assert the course status is unchanged
        try {
            Livewire::actingAs($student)
                ->test(AdminCourseReview::class)
                ->call('approveCourse', $course->id);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // expected
        }

        $this->assertEquals(CourseStatus::Review, $course->fresh()->status);
    }
}
