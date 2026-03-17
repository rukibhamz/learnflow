<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentProtectionTest extends TestCase
{
    use RefreshDatabase;

    private function createEnrolledStudentWithLesson(): array
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);
        $course = Course::factory()->create([
            'instructor_id' => $instructor->id,
            'status' => 'published',
        ]);
        $section = Section::create([
            'course_id' => $course->id,
            'title' => 'Section 1',
            'order' => 1,
        ]);
        $lesson = Lesson::create([
            'section_id' => $section->id,
            'title' => 'Test Lesson',
            'type' => 'video',
            'content_url' => 'https://www.youtube.com/embed/test',
            'order' => 1,
        ]);
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
        ]);

        return compact('student', 'course', 'lesson', 'instructor');
    }

    public function test_learn_page_has_content_protection_headers()
    {
        $data = $this->createEnrolledStudentWithLesson();

        $response = $this->actingAs($data['student'])
            ->get(route('learn.show', $data['course']->slug));

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Cache-Control');
        $response->assertHeader('Referrer-Policy', 'same-origin');
    }

    public function test_unenrolled_user_cannot_access_learn_page()
    {
        $data = $this->createEnrolledStudentWithLesson();
        $stranger = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($stranger)
            ->get(route('learn.show', $data['course']->slug));

        $response->assertRedirect();
    }

    public function test_protected_video_endpoint_requires_enrollment()
    {
        $data = $this->createEnrolledStudentWithLesson();
        $stranger = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($stranger)
            ->get(route('media.lesson.video', $data['lesson']));

        $response->assertForbidden();
    }

    public function test_enrolled_user_can_access_video_endpoint()
    {
        $data = $this->createEnrolledStudentWithLesson();

        $response = $this->actingAs($data['student'])
            ->get(route('media.lesson.video', $data['lesson']));

        $response->assertRedirect();
    }

    public function test_instructor_can_access_own_course_media()
    {
        $data = $this->createEnrolledStudentWithLesson();

        $response = $this->actingAs($data['instructor'])
            ->get(route('media.lesson.video', $data['lesson']));

        $response->assertRedirect();
    }

    public function test_admin_can_access_any_course_media()
    {
        $data = $this->createEnrolledStudentWithLesson();
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('media.lesson.video', $data['lesson']));

        $response->assertRedirect();
    }

    public function test_pdf_endpoint_returns_403_for_unenrolled()
    {
        $data = $this->createEnrolledStudentWithLesson();
        $stranger = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($stranger)
            ->get(route('media.lesson.pdf', $data['lesson']));

        $response->assertForbidden();
    }

    public function test_content_protection_disabled_skips_headers()
    {
        config(['content-protection.enabled' => false]);

        $data = $this->createEnrolledStudentWithLesson();

        $response = $this->actingAs($data['student'])
            ->get(route('learn.show', $data['course']->slug));

        $response->assertOk();
        $this->assertNotEquals('nosniff', $response->headers->get('X-Content-Type-Options'));
    }
}
