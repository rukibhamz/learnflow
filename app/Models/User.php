<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasRoles, InteractsWithMedia, Notifiable;

    /** @use HasFactory<UserFactory> */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'google_id',
        'github_id',
        'avatar',
        'bio',
        'website',
        'social_links',
        'wishlist',
        'notification_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'suspended_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',
            'wishlist' => 'array',
            'notification_preferences' => 'array',
        ];
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    protected $appends = [
        'avatar_url',
        'enrolled_course_count',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->useFallbackUrl('https://ui-avatars.com/api/?name=U&background=EEF1FF&color=1A43E0');
    }

    public function getAvatarUrlAttribute(): string
    {
        $media = $this->getFirstMedia('avatar');
        if ($media) {
            return $media->getUrl();
        }
        if (! empty($this->attributes['avatar'])) {
            return $this->attributes['avatar'];
        }

        return $this->getDefaultAvatarUrl();
    }

    public function getEnrolledCourseCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    protected function getDefaultAvatarUrl(): string
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'U') . '&background=EEF1FF&color=1A43E0';
    }

    public function scopeInstructors(Builder $query): Builder
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'instructor'));
    }

    public function scopeStudents(Builder $query): Builder
    {
        return $query->whereHas('roles', fn ($q) => $q->where('name', 'student'));
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(PushSubscription::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class, 'instructor_id');
    }

    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot(['enrolled_at', 'expires_at', 'completed_at'])
            ->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Determine if the user has verified their email address.
     * Admins and instructors are always considered verified.
     */
    public function hasVerifiedEmail(): bool
    {
        // Admins and instructors are never blocked by email verification
        if ($this->hasRole(['admin', 'instructor'])) {
            return true;
        }

        if (config('settings.mail_require_verification') === '0') {
            return true;
        }

        return parent::hasVerifiedEmail();
    }

    /**
     * Send the email verification notification (queued).
     */
    public function sendEmailVerificationNotification(): void
    {
        if (config('settings.mail_require_verification') === '0') {
            return;
        }

        $this->notify(new \App\Notifications\QueuedVerifyEmail);
    }
}
