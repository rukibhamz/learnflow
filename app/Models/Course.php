<?php

namespace App\Models;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Searchable, SoftDeletes;

    protected $fillable = [
        'instructor_id',
        'title',
        'category',
        'slug',
        'description',
        'short_description',
        'thumbnail',
        'price',
        'level',
        'language',
        'status',
        'requirements',
        'outcomes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'level' => CourseLevel::class,
            'status' => CourseStatus::class,
            'requirements' => 'array',
            'outcomes' => 'array',
        ];
    }

    protected $appends = [
        'average_rating',
        'enrolled_count',
        'completion_rate',
        'total_lessons_count',
        'total_duration_seconds',
    ];

    public function getAverageRatingAttribute(): float
    {
        return (float) $this->reviews()->whereNotNull('approved_at')->avg('rating');
    }

    public function getEnrolledCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    public function getCompletionRateAttribute(): ?float
    {
        $enrolled = $this->enrollments()->count();
        if ($enrolled === 0) {
            return null;
        }
        $completed = $this->enrollments()->whereNotNull('completed_at')->count();

        return round(($completed / $enrolled) * 100, 1);
    }

    public function getTotalLessonsCountAttribute(): int
    {
        return $this->lessons()->count();
    }

    public function getTotalDurationSecondsAttribute(): int
    {
        return (int) $this->lessons()->sum('duration_seconds');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', CourseStatus::Published);
    }

    public function scopeByInstructor(Builder $query, int $id): Builder
    {
        return $query->where('instructor_id', $id);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Section::class)
            ->orderBy('sections.order')
            ->orderBy('lessons.order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
