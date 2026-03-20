<?php

namespace Tests\Feature;

use App\Events\CourseCompleted;
use App\Events\UserEnrolled;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\CourseCompletedNotification;
use App\Notifications\NewEnrollmentNotification;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_instructor_receives_enrollment_notification(): void
    {
        Notification::fake();

        $instructor = User::factory()->create();
        $instructor->assignRole('instructor');

        $student = User::factory()->create();
        $student->assignRole('student');

        $course = Course::factory()->published()->create(['instructor_id' => $instructor->id]);
        $enrollment = Enrollment::factory()->create([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        event(new UserEnrolled($enrollment));

        Notification::assertSentTo($instructor, NewEnrollmentNotification::class);
    }

    public function test_student_receives_course_completed_notification(): void
    {
        Queue::fake();
        Notification::fake();

        $student = User::factory()->create();
        $student->assignRole('student');

        $course = Course::factory()->published()->create();

        event(new CourseCompleted($student, $course, now()));

        Notification::assertSentTo($student, CourseCompletedNotification::class);
    }

    public function test_notification_api_returns_user_notifications(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');

        $course = Course::factory()->published()->create();
        $user->notify(new CourseCompletedNotification($course));

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/notifications');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('unread_count', 1);
    }

    public function test_notification_api_can_mark_as_read(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');

        $course = Course::factory()->published()->create();
        $user->notify(new CourseCompletedNotification($course));

        $notification = $user->notifications()->first();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson("/api/notifications/{$notification->id}/read");

        $response->assertOk();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_notification_api_can_mark_all_read(): void
    {
        $user = User::factory()->create();
        $user->assignRole('student');

        $course1 = Course::factory()->published()->create();
        $course2 = Course::factory()->published()->create();
        $user->notify(new CourseCompletedNotification($course1));
        $user->notify(new CourseCompletedNotification($course2));

        $this->assertEquals(2, $user->unreadNotifications()->count());

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/notifications/read-all');

        $response->assertOk();
        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }
}
