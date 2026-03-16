<?php

namespace Tests\Feature;

use App\Enums\CourseStatus;
use App\Events\UserEnrolled;
use App\Jobs\SendEnrolmentConfirmationEmail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\EnrolmentService;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EnrolmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function student(): User
    {
        $user = User::factory()->create();
        $user->assignRole('student');
        return $user;
    }

    // ── EnrolmentService unit-style tests ──────────────────────────────────

    public function test_service_enrols_user_in_free_course(): void
    {
        Queue::fake();
        Event::fake();

        $user = $this->student();
        $course = Course::factory()->published()->free()->create();

        $enrollment = app(EnrolmentService::class)->enrol($user, $course);

        $this->assertInstanceOf(Enrollment::class, $enrollment);
        $this->assertDatabaseHas('enrollments', [
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_service_fires_user_enrolled_event(): void
    {
        Event::fake();
        Queue::fake();

        $user = $this->student();
        $course = Course::factory()->published()->free()->create();

        app(EnrolmentService::class)->enrol($user, $course);

        Event::assertDispatched(UserEnrolled::class);
    }

    public function test_service_dispatches_confirmation_email_job(): void
    {
        Queue::fake();
        Event::fake();

        $user = $this->student();
        $course = Course::factory()->published()->free()->create();

        app(EnrolmentService::class)->enrol($user, $course);

        Queue::assertPushed(SendEnrolmentConfirmationEmail::class);
    }

    public function test_service_throws_when_already_enrolled(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->free()->create();
        Enrollment::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('already enrolled');

        app(EnrolmentService::class)->enrol($user, $course);
    }

    public function test_service_throws_when_course_not_published(): void
    {
        $user = $this->student();
        $course = Course::factory()->create(['status' => CourseStatus::Draft, 'price' => 0]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not available');

        app(EnrolmentService::class)->enrol($user, $course);
    }

    public function test_is_already_enrolled_returns_true(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->free()->create();
        Enrollment::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

        $this->assertTrue(app(EnrolmentService::class)->isAlreadyEnrolled($user, $course));
    }

    public function test_is_already_enrolled_returns_false(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->free()->create();

        $this->assertFalse(app(EnrolmentService::class)->isAlreadyEnrolled($user, $course));
    }

    public function test_can_enrol_returns_true_for_eligible_user(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->free()->create();

        $this->assertTrue(app(EnrolmentService::class)->canEnrol($user, $course));
    }

    // ── HTTP controller tests ──────────────────────────────────────────────

    public function test_guest_cannot_enrol(): void
    {
        $course = Course::factory()->published()->free()->create();

        $this->post(route('enrolments.store'), ['course_id' => $course->id])
            ->assertRedirect(route('login'));
    }

    public function test_student_can_enrol_in_free_course(): void
    {
        Queue::fake();
        Event::fake();

        $user = $this->student();
        $course = Course::factory()->published()->free()->create();

        $this->actingAs($user)
            ->post(route('enrolments.store'), ['course_id' => $course->id])
            ->assertRedirect(route('learn.show', $course->slug));

        $this->assertDatabaseHas('enrollments', [
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_already_enrolled_redirects_to_learn_page(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->free()->create();
        Enrollment::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

        $this->actingAs($user)
            ->post(route('enrolments.store'), ['course_id' => $course->id])
            ->assertRedirect(route('learn.show', $course->slug));

        // Still only one enrollment
        $this->assertDatabaseCount('enrollments', 1);
    }

    public function test_paid_course_redirects_to_checkout(): void
    {
        $user = $this->student();
        $course = Course::factory()->published()->create(['price' => 49.99]);

        $this->actingAs($user)
            ->post(route('enrolments.store'), ['course_id' => $course->id])
            ->assertRedirect(route('checkout.course', $course->slug));
    }

    public function test_enrolment_requires_valid_course_id(): void
    {
        $user = $this->student();

        $this->actingAs($user)
            ->post(route('enrolments.store'), ['course_id' => 99999])
            ->assertSessionHasErrors('course_id');
    }
}
