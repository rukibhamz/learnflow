<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\User;
use App\Policies\CoursePolicy;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected CoursePolicy $policy;
    protected User $admin;
    protected User $instructor;
    protected User $otherInstructor;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->policy = new CoursePolicy;

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->instructor = User::factory()->create();
        $this->instructor->assignRole('instructor');

        $this->otherInstructor = User::factory()->create();
        $this->otherInstructor->assignRole('instructor');

        $this->student = User::factory()->create();
        $this->student->assignRole('student');
    }

    public function test_anyone_can_view_any_courses(): void
    {
        $this->assertTrue($this->policy->viewAny(null));
        $this->assertTrue($this->policy->viewAny($this->student));
    }

    public function test_anyone_can_view_a_course(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);
        $this->assertTrue($this->policy->view(null, $course));
        $this->assertTrue($this->policy->view($this->student, $course));
    }

    public function test_instructor_and_admin_can_create_courses(): void
    {
        $this->assertTrue($this->policy->create($this->instructor));
        $this->assertTrue($this->policy->create($this->admin));
    }

    public function test_student_cannot_create_courses(): void
    {
        $this->assertFalse($this->policy->create($this->student));
    }

    public function test_instructor_can_update_their_own_course(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);
        $this->assertTrue($this->policy->update($this->instructor, $course));
    }

    public function test_instructor_cannot_update_another_instructors_course(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->otherInstructor->id]);
        $this->assertFalse($this->policy->update($this->instructor, $course));
    }

    public function test_admin_can_update_any_course(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);
        $this->assertTrue($this->policy->update($this->admin, $course));
    }

    public function test_instructor_can_delete_their_own_course(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);
        $this->assertTrue($this->policy->delete($this->instructor, $course));
    }

    public function test_instructor_cannot_delete_another_instructors_course(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->otherInstructor->id]);
        $this->assertFalse($this->policy->delete($this->instructor, $course));
    }

    public function test_admin_can_delete_any_course(): void
    {
        $course = Course::factory()->create(['instructor_id' => $this->instructor->id]);
        $this->assertTrue($this->policy->delete($this->admin, $course));
    }
}
