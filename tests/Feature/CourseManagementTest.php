<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use App\Livewire\InstructorCourseIndex;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $instructor;
    protected User $otherInstructor;
    protected User $admin;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->instructor = User::factory()->create();
        $this->instructor->assignRole('instructor');

        $this->otherInstructor = User::factory()->create();
        $this->otherInstructor->assignRole('instructor');

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->student = User::factory()->create();
        $this->student->assignRole('student');
    }

    // ── Listing ───────────────────────────────────────────────────────────────

    public function test_instructor_sees_only_their_own_courses(): void
    {
        $mine = Course::factory()->create(['instructor_id' => $this->instructor->id]);
        Course::factory()->create(['instructor_id' => $this->otherInstructor->id]);

        Livewire::actingAs($this->instructor)
            ->test(InstructorCourseIndex::class)
            ->assertSee($mine->title)
            ->assertDontSee($this->otherInstructor->courses->first()->title);
    }

    public function test_instructor_can_search_courses_by_title(): void
    {
        Course::factory()->create(['instructor_id' => $this->instructor->id, 'title' => 'Laravel Basics']);
        Course::factory()->create(['instructor_id' => $this->instructor->id, 'title' => 'Vue Fundamentals']);

        Livewire::actingAs($this->instructor)
            ->test(InstructorCourseIndex::class)
            ->set('search', 'Laravel')
            ->assertSee('Laravel Basics')
            ->assertDontSee('Vue Fundamentals');
    }

    public function test_instructor_can_filter_courses_by_status(): void
    {
        Course::factory()->create(['instructor_id' => $this->instructor->id, 'status' => CourseStatus::Draft, 'title' => 'Draft Course']);
        Course::factory()->create(['instructor_id' => $this->instructor->id, 'status' => CourseStatus::Published, 'title' => 'Published Course']);

        Livewire::actingAs($this->instructor)
            ->test(InstructorCourseIndex::class)
            ->set('statusFilter', CourseStatus::Draft->value)
            ->assertSee('Draft Course')
            ->assertDontSee('Published Course');
    }

    // ── Submit for review ─────────────────────────────────────────────────────

    public function test_instructor_can_submit_draft_course_for_review(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'status' => CourseStatus::Draft,
        ]);

        Livewire::actingAs($this->instructor)
            ->test(InstructorCourseIndex::class)
            ->call('submitForReview', $course->id);

        $this->assertEquals(CourseStatus::Review, $course->fresh()->status);
    }

    public function test_instructor_cannot_submit_already_published_course_for_review(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'status' => CourseStatus::Published,
        ]);

        Livewire::actingAs($this->instructor)
            ->test(InstructorCourseIndex::class)
            ->call('submitForReview', $course->id);

        $this->assertEquals(CourseStatus::Published, $course->fresh()->status);
    }

    public function test_instructor_cannot_submit_another_instructors_course_for_review(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->otherInstructor->id,
            'status' => CourseStatus::Draft,
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($this->instructor)
            ->test(InstructorCourseIndex::class)
            ->call('submitForReview', $course->id);
    }

    // ── Unpublish ─────────────────────────────────────────────────────────────

    public function test_instructor_can_unpublish_their_published_course(): void
    {
        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->id,
            'status' => CourseStatus::Published,
        ]);

        Livewire::actingAs($this->instructor)
            ->test(InstructorCourseIndex::class)
            ->call('unpublish', $course->id);

        $this->assertEquals(CourseStatus::Draft, $course->fresh()->status);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    public function test_instructor_can_delete_their_own_course(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);

        Livewire::actingAs($this->instructor)
            ->test(InstructorCourseIndex::class)
            ->call('confirmDelete', $course->id)
            ->call('deleteCourse');

        $this->assertSoftDeleted('courses', ['id' => $course->id]);
    }

    public function test_instructor_cannot_delete_another_instructors_course(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->otherInstructor->id]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        Livewire::actingAs($this->instructor)
            ->test(InstructorCourseIndex::class)
            ->call('confirmDelete', $course->id);
    }

    // ── Duplicate ─────────────────────────────────────────────────────────────

    public function test_instructor_can_duplicate_their_course(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id, 'title' => 'Original Course']);
        $section = Section::factory()->create(['course_id' => $course->id]);
        Lesson::factory()->create(['section_id' => $section->id]);

        Livewire::actingAs($this->instructor)
            ->test(InstructorCourseIndex::class)
            ->call('duplicateCourse', $course->id);

        $this->assertDatabaseHas('courses', ['title' => 'Original Course (Copy)', 'status' => CourseStatus::Draft->value]);

        $copy = Course::where('title', 'Original Course (Copy)')->first();
        $this->assertNotNull($copy);
        $this->assertCount(1, $copy->sections);
        $this->assertCount(1, $copy->lessons);
    }

    public function test_duplicated_course_has_unique_slug(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);

        Livewire::actingAs($this->instructor)
            ->test(InstructorCourseIndex::class)
            ->call('duplicateCourse', $course->id);

        $copy = Course::where('title', $course->title . ' (Copy)')->first();
        $this->assertNotEquals($course->slug, $copy->slug);
    }
}
