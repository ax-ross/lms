<?php

namespace Database\Factories;

use App\Models\CourseSection;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->text(255),
            'content' => fake()->randomHtml(),
            'section_id' => CourseSection::factory(),
        ];
    }
}
