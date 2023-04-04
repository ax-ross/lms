<?php

namespace Tests\Feature\Lesson;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\LessonImage;
use App\Models\User;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoreLessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_store_lesson(): void
    {
        Storage::fake('public');

        $section = CourseSection::factory()->create();
        $teacher = $section->course->teacher;
        $images = LessonImage::factory()->count(3)->create();

        $payload = [
            'title' => 'lesson title',
            'content' => 'lesson content',
            'section_id' => $section->id,
            'imagePaths' => $this->generateAbsolutePathsForImages($images),
        ];

        $response = $this->actingAs($teacher)->postJson('/lessons', $payload);

        $response->assertStatus(201);
        $this->assertModelExists($lesson = $section->lessons()->find($response->getData()->data->id));

        foreach ($images as $image) {
            $this->assertTrue($lesson->images->contains($image));
        }
    }

    public function test_teacher_cant_store_lesson_with_invalid_data(): void
    {
        $section = CourseSection::factory()->create();
        $teacher = $section->course->teacher;

        $payload = [
            'title' => Str::random(256),
            'content' => '',
            'section_id' => 'invalid',
            'imagePaths' => [1, 2, 3],
        ];

        $response = $this->actingAs($teacher)->postJson('/lessons', $payload);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message', 'errors' => ['title', 'content', 'section_id', 'imagePaths.0', 'imagePaths.1', 'imagePaths.2']
        ]);
    }

    public function test_student_cant_store_lesson(): void
    {
        Storage::fake('public');

        $course = Course::factory()->has(User::factory(), 'students')
            ->has(CourseSection::factory(), 'sections')
            ->create();
        $section = $course->sections->first();
        $student = $course->students->first();

        $images = LessonImage::factory()->count(3)->create();

        $payload = [
            'title' => 'lesson title',
            'content' => 'lesson content',
            'section_id' => $section->id,
            'imagePaths' => $this->generateAbsolutePathsForImages($images),
        ];

        $response = $this->actingAs($student)->postJson('/lessons', $payload);

        $response->assertStatus(403);
        $this->assertFalse($section->lessons->contains('title', 'lesson_title'));
    }

    private function generateAbsolutePathsForImages(array|Collection $images): array
    {
        return $images->pluck('path')->map(fn($item) => url('storage', $item))->all();
    }
}
