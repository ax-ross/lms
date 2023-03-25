<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\LessonImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LessonImage>
 */
class LessonImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => fake()->imageUrl(),
            'lesson_id' => Lesson::factory(),
        ];
    }
}
