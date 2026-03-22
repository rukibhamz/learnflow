<?php

namespace App\Providers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Policies\CertificatePolicy;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\QuizAttemptPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Enrollment::class, EnrollmentPolicy::class);
        Gate::policy(QuizAttempt::class, QuizAttemptPolicy::class);
        Gate::policy(Certificate::class, CertificatePolicy::class);

        // Event wiring (Laravel 12 app without EventServiceProvider)
        Event::listen(\App\Events\CourseCompleted::class, \App\Listeners\QueueIssueCertificate::class);
        Event::listen(\App\Events\LessonCompleted::class, \App\Listeners\CheckCourseCompletion::class);

        Event::listen(\App\Events\UserEnrolled::class, function (\App\Events\UserEnrolled $event) {
            $instructor = $event->enrollment->course->instructor;
            if ($instructor && config('settings.notify_instructor_new_enrollment', '1')) {
                $instructor->notify(new \App\Notifications\NewEnrollmentNotification($event->enrollment));
            }

            if (config('settings.notify_enrollment_confirmation', '1')) {
                $event->enrollment->user->notify(new \App\Notifications\CourseEnrolledNotification($event->enrollment));
            }
        });

        Event::listen(\App\Events\CourseCompleted::class, function (\App\Events\CourseCompleted $event) {
            if (config('settings.notify_course_completion', '1')) {
                $event->user->notify(new \App\Notifications\CourseCompletedNotification($event->course));
            }
        });

        // Load settings into config
        try {
            if (Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::all();
                foreach ($settings as $setting) {
                    if (str_starts_with($setting->key, 'mail_')) {
                        // Map mail settings to Laravel config keys
                        $mappings = [
                            'mail_mailer' => 'mail.default',
                            'mail_host' => 'mail.mailers.smtp.host',
                            'mail_port' => 'mail.mailers.smtp.port',
                            'mail_username' => 'mail.mailers.smtp.username',
                            'mail_password' => 'mail.mailers.smtp.password',
                            'mail_encryption' => 'mail.mailers.smtp.encryption',
                            'mail_from_address' => 'mail.from.address',
                            'mail_from_name' => 'mail.from.name',
                            'mail_ses_key' => 'services.ses.key',
                            'mail_ses_secret' => 'services.ses.secret',
                            'mail_ses_region' => 'services.ses.region',
                            'mail_mailgun_domain' => 'services.mailgun.domain',
                            'mail_mailgun_secret' => 'services.mailgun.secret',
                            'mail_mailgun_endpoint' => 'services.mailgun.endpoint',
                            'mail_postmark_token' => 'services.postmark.token',
                        ];

                        if (isset($mappings[$setting->key])) {
                            config([$mappings[$setting->key] => $setting->value]);
                        } else {
                            config(['settings.' . $setting->key => $setting->value]);
                        }
                    } else {
                        config(['settings.' . $setting->key => $setting->value]);
                    }
                }
            }

            // Ensure default for email verification
            if (! config()->has('settings.mail_require_verification')) {
                config(['settings.mail_require_verification' => \App\Models\Setting::get('mail_require_verification', '1')]);
            }
        } catch (\Throwable $e) {
            // Database not yet available (e.g. during install)
        }

        if (config('app.env') === 'production') {
            $appUrl = config('app.url');
            if ($appUrl && str_starts_with($appUrl, 'https:')) {
                URL::forceScheme('https');
            }
        }

        // Force root URL to include subdirectory prefix (e.g. /learnflow)
        $appUrl = config('app.url');
        if ($appUrl) {
            URL::forceRootUrl($appUrl);
        }

        // Share branding with all views
        View::composer('*', function ($view) {
            $siteName = config('app.name', 'LearnFlow');
            $siteColor = '#1a42e0';
            $siteLogoUrl = null;

            try {
                if (Schema::hasTable('settings')) {
                    $siteName = \App\Models\Setting::get('site_name', $siteName);
                    $siteColor = \App\Models\Setting::get('site_color', $siteColor);
                    $logoPath = \App\Models\Setting::get('site_logo');
                    $siteLogoUrl = $logoPath && Storage::disk('public')->exists($logoPath)
                        ? Storage::disk('public')->url($logoPath)
                        : null;
                    
                    $faviconPath = \App\Models\Setting::get('site_favicon');
                    $siteFaviconUrl = $faviconPath && Storage::disk('public')->exists($faviconPath)
                        ? Storage::disk('public')->url($faviconPath)
                        : null;
                }
            } catch (\Throwable $e) {
                // Database not yet available
            }

            $view->with(compact('siteName', 'siteColor', 'siteLogoUrl', 'siteFaviconUrl'));
        });
    }
}
