<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    /**
     * Determine whether the user can create an enrollment.
     */
    public function create(User $user, Course $course): bool
    {
        if (! $user->hasRole('student')) {
            return false;
        }

        return ! $user->enrollments()->where('course_id', $course->id)->exists();
    }

    /**
     * Determine whether the user can delete the enrollment.
     */
    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $user->hasRole('admin');
    }
}
