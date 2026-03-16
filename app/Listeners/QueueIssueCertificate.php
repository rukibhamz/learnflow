<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Jobs\IssueCertificate;

class QueueIssueCertificate
{
    public function __invoke(CourseCompleted $event): void
    {
        IssueCertificate::dispatch($event->user->id, $event->course->id, $event->completedAt);
    }
}

