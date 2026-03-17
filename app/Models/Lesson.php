<?php

namespace App\Models;

use App\Enums\LessonType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Lesson extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('pdf')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function (Builder $query) {
            $query->orderBy($query->qualifyColumn('order'));
        });

        $flush = function (self $lesson): void {
            $courseId = $lesson->section?->course_id ?? null;
            if (! $courseId) {
                return;
            }
            try {
                Cache::tags(['courses', "course:{$courseId}"])->flush();
            } catch (\Throwable) {
                Cache::forget("course:{$courseId}:curriculum:v1");
            }
        };

        static::saved($flush);
        static::deleted($flush);
    }

    protected $fillable = [
        'section_id',
        'title',
        'type',
        'content_url',
        'content_body',
        'duration_seconds',
        'is_preview',
        'unlock_after_days',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'type' => LessonType::class,
            'is_preview' => 'boolean',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class, 'lesson_id');
    }
}
