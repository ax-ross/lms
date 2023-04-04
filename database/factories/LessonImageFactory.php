<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\LessonImage;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

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
            'path' => UploadedFile::fake()->image('test.jpg')->store('/lesson-images', 'public'),
            'lesson_id' => Lesson::factory(),
        ];
    }
}
