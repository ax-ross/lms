<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CourseSection>
 */
class CourseSectionFactory extends Factory
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
            'description' => fake()->text(),
            'section_number' => fake()->randomNumber(),
            'course_id' => Course::factory(),
        ];
    }
}
