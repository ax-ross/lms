<?php

namespace Tests\Feature\Task;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_delete_lesson(): void
    {
        $task = Task::factory()->create();
        $teacher = $task->lesson->section->course->teacher;

        $response = $this->actingAs($teacher)->deleteJson("/lessons/{$task->lesson->id}/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertModelMissing($task);
    }

    public function test_student_cant_delete_lesson(): void
    {
        $course = Course::factory()
            ->has(User::factory(), 'students')
            ->has(CourseSection::factory()->has(Lesson::factory()->has(Task::factory())), 'sections')
            ->create();
        $lesson = $course->sections->first()->lessons->first();
        $task = $lesson->tasks->first();
        $student = $course->students->first();

        $response = $this->actingAs($student)->deleteJson("/lessons/$lesson->id/tasks/$task->id");

        $response->assertStatus(403);
        $this->assertModelExists($task);
    }
}
