<?php

namespace Tests\Feature\Task;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Image;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoreTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_store_task(): void
    {
        Storage::fake('public');

        $lesson = Lesson::factory()->create();
        $teacher = $lesson->section->course->teacher;
        $images = Image::factory()->count(3)->create();

        $payload = [
            'title' => 'task title',
            'description' => 'task description',
            'lesson_id' => $lesson->id,
            'is_required' => true,
            'imagePaths' => $this->generateAbsolutePathsForImages($images),
        ];

        $response = $this->actingAs($teacher)->postJson("/lessons/{$lesson->id}/tasks", $payload);

        $response->assertStatus(201);
        $this->assertModelExists($task = $lesson->tasks()->find($response->getData()->data->id));

        foreach ($images as $image) {
            $this->assertTrue($task->images->contains($image));
        }
    }

    public function test_teacher_cant_store_task_with_invalid_data(): void
    {
        $lesson = Lesson::factory()->create();
        $teacher = $lesson->section->course->teacher;

        $payload = [
            'title' => Str::random(256),
            'description' => '',
            'lesson_id' => 'invalid',
            'imagePaths' => [1, 2, 3],
            'is_required' => 123
        ];

        $response = $this->actingAs($teacher)->postJson("/lessons/{$lesson->id}/tasks", $payload);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message', 'errors' => [
                'title', 'description', 'lesson_id', 'imagePaths.0', 'imagePaths.1', 'imagePaths.2', 'is_required'
            ]
        ]);
    }

    public function test_student_cant_store_task(): void
    {
        Storage::fake('public');

        $course = Course::factory()->has(User::factory(), 'students')
            ->has(CourseSection::factory()->has(Lesson::factory(), 'lessons'), 'sections')
            ->create();

        $lesson = $course->sections->first()->lessons->first();
        $student = $course->students->first();

        $images = Image::factory()->count(3)->create();

        $payload = [
            'title' => 'task title',
            'description' => 'task description',
            'lesson_id' => $lesson->id,
            'imagePaths' => $this->generateAbsolutePathsForImages($images),
            'is_required' => true,
        ];

        $response = $this->actingAs($student)->postJson("/lessons/{$lesson->id}/tasks", $payload);

        $response->assertStatus(403);
        $this->assertFalse($lesson->tasks->contains('title', 'task_title'));
    }

    private function generateAbsolutePathsForImages(array|Collection $images): array
    {
        return $images->pluck('path')->map(fn($item) => url('storage', $item))->all();
    }
}
