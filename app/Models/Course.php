<?php

namespace App\Models;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Jobs\ScoutUpdateCourse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, Searchable, SoftDeletes;

    protected static function booted(): void
    {
        // When status changes to published → index; to anything else → remove
        static::updated(function (self $course) {
            if ($course->wasChanged('status')) {
                if ($course->status === CourseStatus::Published) {
                    $course->searchable();
                } else {
                    $course->unsearchable();
                }
            }
        });

        // Hard delete → remove from index
        static::deleted(function (self $course) {
            if (! $course->isForceDeleting()) {
                return; // soft delete — leave in index until status changes
            }
            $course->unsearchable();
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(225)
            ->sharpen(10)
            ->nonQueued();
    }

    protected $fillable = [
        'instructor_id',
        'category_id',
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
        'prerequisite_ids',
        'certificate_template_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'level' => CourseLevel::class,
            'status' => CourseStatus::class,
            'requirements' => 'array',
            'outcomes' => 'array',
            'prerequisite_ids' => 'array',
        ];
    }

    /**
     * Courses that must be completed before enrolling in this one.
     */
    public function prerequisites(): \Illuminate\Database\Eloquent\Collection
    {
        $ids = $this->prerequisite_ids ?? [];

        return $ids ? self::whereIn('id', $ids)->get() : new \Illuminate\Database\Eloquent\Collection();
    }

    public function certificateTemplate(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    /**
     * Check if a user has completed all prerequisite courses.
     */
    public function prerequisitesMet(User $user): bool
    {
        $ids = $this->prerequisite_ids ?? [];
        if (empty($ids)) {
            return true;
        }

        $completedCourseIds = Enrollment::where('user_id', $user->id)
            ->whereIn('course_id', $ids)
            ->whereNotNull('completed_at')
            ->pluck('course_id')
            ->toArray();

        return count(array_intersect($ids, $completedCourseIds)) === count($ids);
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

    public function toSearchableArray(): array
    {
        $this->loadMissing('instructor');

        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'slug'              => $this->slug,
            'short_description' => $this->short_description,
            'instructor_name'   => $this->instructor?->name,
            'level'             => $this->level?->value,
            'language'          => $this->language,
            'price'             => (float) $this->price,
            'average_rating'    => (float) $this->getAverageRatingAttribute(),
            'enrolled_count'    => $this->getEnrolledCountAttribute(),
            'category_id'       => $this->category_id,
            'category'          => $this->category?->name,
            'status'            => $this->status?->value,
            'created_at'        => $this->created_at?->timestamp,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->status === CourseStatus::Published;
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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

    public function cacheTags(): array
    {
        return ['courses', "course:{$this->id}"];
    }

    private function cacheRepositoryWithTags(): ?\Illuminate\Cache\TaggedCache
    {
        try {
            /** @var \Illuminate\Cache\TaggedCache $repo */
            $repo = Cache::tags($this->cacheTags());
            return $repo;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Cached, lightweight curriculum for rendering (sections + lessons).
     */
    public function cachedCurriculum(int $ttlSeconds = 300): array
    {
        $key = "course:{$this->id}:curriculum:v1";
        $callback = function (): array {
            $sections = $this->sections()
                ->select(['id', 'course_id', 'title', 'description', 'order'])
                ->with(['lessons' => function ($q) {
                    $q->select([
                        'id',
                        'section_id',
                        'title',
                        'type',
                        'content_url',
                        'content_body',
                        'duration_seconds',
                        'is_preview',
                        'unlock_after_days',
                        'order',
                    ])->withoutGlobalScope('ordered')->orderBy('order');
                }])
                ->withoutGlobalScope('ordered')
                ->orderBy('order')
                ->get();

            return [
                'sections' => $sections->toArray(),
            ];
        };

        return $this->cacheRepositoryWithTags()?->remember($key, $ttlSeconds, $callback)
            ?? Cache::remember($key, $ttlSeconds, $callback);
    }

    public static function cachedLanguages(int $ttlSeconds = 3600): array
    {
        $key = 'courses:filters:languages:v1';
        $callback = fn () => self::published()
            ->whereNotNull('language')
            ->distinct()
            ->orderBy('language')
            ->pluck('language')
            ->filter()
            ->values()
            ->all();

        try {
            return Cache::tags(['courses', 'filters'])->remember($key, $ttlSeconds, $callback);
        } catch (\Throwable) {
            return Cache::remember($key, $ttlSeconds, $callback);
        }
    }

    public static function cachedCategories(int $ttlSeconds = 3600): \Illuminate\Support\Collection
    {
        $key = 'courses:filters:categories:v2';
        $callback = fn () => Category::where('is_active', true)
            ->whereHas('courses', fn($q) => $q->published())
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        try {
            return Cache::tags(['courses', 'filters'])->remember($key, $ttlSeconds, $callback);
        } catch (\Throwable) {
            return Cache::remember($key, $ttlSeconds, $callback);
        }
    }
}
