<?php

namespace Database\Factories;

use App\Enums\LessonType;
use App\Models\Lesson;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    protected $model = Lesson::class;

    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'title' => fake()->sentence(4),
            'type' => LessonType::Text,
            'content_body' => fake()->paragraphs(2, true),
            'content_url' => null,
            'duration_seconds' => fake()->numberBetween(60, 3600),
            'is_preview' => false,
            'order' => fake()->numberBetween(1, 20),
        ];
    }

    public function video(): static
    {
        return $this->state([
            'type' => LessonType::Video,
            'content_url' => 'https://example.com/video.mp4',
            'content_body' => null,
        ]);
    }

    public function preview(): static
    {
        return $this->state(['is_preview' => true]);
    }
}
