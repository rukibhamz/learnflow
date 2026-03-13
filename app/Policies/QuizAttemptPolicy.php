<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;

class QuizAttemptPolicy
{
    /**
     * Determine whether the user can create a quiz attempt.
     */
    public function create(User $user, Quiz $quiz): bool
    {
        return $user->enrolledCourses()->where('course_id', $quiz->course_id)->exists();
    }
}
