<?php

namespace App\Jobs;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ScoutUpdateCourse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(public readonly int $courseId) {}

    /**
     * Dispatch only if no job is already queued for this course_id.
     * Uses a cache lock as a debounce flag (TTL = queue delay + buffer).
     */
    public static function dispatchDebounced(int $courseId, int $delaySeconds = 30): void
    {
        $key = "scout_update_course_{$courseId}";

        if (Cache::has($key)) {
            return; // already queued — skip
        }

        Cache::put($key, true, $delaySeconds + 10);

        static::dispatch($courseId)->delay(now()->addSeconds($delaySeconds));
    }

    public function handle(): void
    {
        $course = Course::find($this->courseId);

        if (! $course) {
            return;
        }

        if ($course->shouldBeSearchable()) {
            $course->searchable();
        } else {
            $course->unsearchable();
        }

        // Clear debounce flag so future changes can queue again
        Cache::forget("scout_update_course_{$this->courseId}");
    }
}
