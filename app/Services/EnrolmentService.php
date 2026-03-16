<?php

namespace App\Services;

use App\Enums\CourseStatus;
use App\Events\UserEnrolled;
use App\Jobs\SendEnrolmentConfirmationEmail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EnrolmentService
{
    public function enrol(User $user, Course $course): Enrollment
    {
        $this->validateEnrolment($user, $course);

        return DB::transaction(function () use ($user, $course) {
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
            ]);

            event(new UserEnrolled($enrollment));

            SendEnrolmentConfirmationEmail::dispatch($enrollment);

            return $enrollment;
        });
    }

    protected function validateEnrolment(User $user, Course $course): void
    {
        if ($this->isAlreadyEnrolled($user, $course)) {
            throw new \Exception('You are already enrolled in this course.');
        }

        if ($course->status !== CourseStatus::Published) {
            throw new \Exception('This course is not available for enrolment.');
        }
    }

    public function isAlreadyEnrolled(User $user, Course $course): bool
    {
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
    }

    public function canEnrol(User $user, Course $course): bool
    {
        return !$this->isAlreadyEnrolled($user, $course) 
            && $course->status === CourseStatus::Published;
    }
}
