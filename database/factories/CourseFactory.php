<?php

namespace Database\Factories;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'instructor_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(4),
            'description' => fake()->paragraphs(2, true),
            'short_description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 0, 200),
            'level' => fake()->randomElement(CourseLevel::cases()),
            'language' => 'English',
            'status' => CourseStatus::Draft,
            'requirements' => [],
            'outcomes' => [],
        ];
    }

    public function published(): static
    {
        return $this->state(['status' => CourseStatus::Published]);
    }

    public function inReview(): static
    {
        return $this->state(['status' => CourseStatus::Review]);
    }

    public function free(): static
    {
        return $this->state(['price' => 0]);
    }
}
