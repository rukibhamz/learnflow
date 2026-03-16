<?php

namespace App\\Events;

use App\\Models\\Lesson;
use App\\Models\\User;
use Illuminate\\Foundation\\Events\\Dispatchable;
use Illuminate\\Queue\\SerializesModels;

class LessonCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public Lesson $lesson,
        public \\DateTimeInterface $completedAt,
    ) {
    }
}

