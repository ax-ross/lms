<?php

namespace Tests\Feature\Task;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Image;
use App\Models\Lesson;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class UpdateTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_update_task(): void
    {
        Storage::fake('public');

        [$task, $oldImages] = $this->create_task_with_images();
        $teacher = $task->lesson->section->course->teacher;
        $images = Image::factory()->count(3)->create();
        $payload = $this->generate_payload_for_task_update($task, $images);

        $response = $this->actingAs($teacher)->patchJson("/lessons/{$task->lesson->id}/tasks/{$task->id}", $payload);

        $response->assertStatus(200);
        $this->assertTrue($task->fresh()->lesson_id === $payload['lesson_id']);

        foreach ($oldImages as $image) {
            $this->assertFalse($task->images->contains($image));
        }

        foreach ($images as $image) {
            $this->assertTrue($task->images->contains($image));
        }
    }

    public function test_teacher_cant_update_task_with_invalid_data(): void
    {
        $task = Task::factory()->create();
        $teacher = $task->lesson->section->course->teacher;

        $payload = [
            'title' => 123,
            'description' => '',
            'lesson_id' => Lesson::max('id') + 1,
            'is_required' => 3,
        ];


        $response = $this->actingAs($teacher)->patchJson("/lessons/{$task->lesson->id}/tasks/{$task->id}", $payload);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['title', 'description', 'lesson_id', 'is_required']]);
    }

    public function test_student_cant_update_task(): void
    {
        $course = Course::factory()
            ->has(User::factory(), 'students')
            ->has(CourseSection::factory()->has(Lesson::factory()->has(Task::factory())), 'sections')
            ->create();

        $lesson = $course->sections->first()->lessons->first();
        $task = $lesson->tasks()->first();
        $student = $course->students->first();
        $newLesson = Lesson::factory()->create();

        $payload = [
            'title' => 'updated lesson title',
            'description' => 'updated lesson content',
            'lesson_id' => $newLesson->id,
        ];

        $response = $this->actingAs($student)->patchJson("/lessons/{$lesson->id}/tasks/{$task->id}", $payload);

        $response->assertStatus(403);
        $this->assertFalse($newLesson->tasks->contains('title', $payload['title']));
    }

    private function create_task_with_images(): array
    {
        $images = Image::factory()->count(3)->create();
        $description = $this->generate_random_html_with_images($images);

        $task = Task::factory()->create(['description' => $description]);

        $task->images()->saveMany($images);

        return [$task, $images];
    }

    private function generate_random_html_with_images(array|Collection $images): string
    {
        $content = fake()->randomHtml();
        foreach ($images as $image) {
            $absPath = url('storage', $image->path);
            $content .= " <img src='{$absPath}' alt='image'>";
        }

        return $content;
    }

    private function generate_payload_for_task_update(Task $task, array|Collection $images): array
    {
        $description = $this->generate_random_html_with_images($images);
        $lesson = Lesson::factory()->create(['section_id' => $task->lesson->section->id]);

        return [
            'title' => 'updated lesson title',
            'description' => $description,
            'lesson_id' => $lesson->id,
            'is_required' => true,
        ];
    }
}
