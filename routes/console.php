<?php

use App\Models\Course;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('enrollments:expire')->dailyAt('00:00');
Schedule::command('coupons:deactivate-expired')->dailyAt('00:00');

Schedule::command('horizon:snapshot')->hourly();

Schedule::command('scout:import', ['model' => Course::class])
    ->sundays()
    ->at('02:00');

// Keep Meilisearch index consistent with DB — runs every Sunday at 02:00
Schedule::command('scout:import', [\App\Models\Course::class])
    ->weeklyOn(0, '02:00')
    ->withoutOverlapping()
    ->runInBackground();
