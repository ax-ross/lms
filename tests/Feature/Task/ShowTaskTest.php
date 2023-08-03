<?php

namespace Tests\Feature\Task;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_task(): void
    {
        $task = Task::factory()->create();
        $teacher = $task->lesson->section->course->teacher;

        $response = $this->actingAs($teacher)->getJson("/lessons/{$task->lesson->id}/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $task->id);
    }

    public function test_student_can_view_task(): void
    {
        $course = Course::factory()
            ->has(User::factory(), 'students')
            ->has(CourseSection::factory()->has(Lesson::factory()->has(Task::factory())), 'sections')
            ->create();

        $lesson = $course->sections->first()->lessons->first();
        $task = $lesson->tasks->first();
        $student = $course->students->first();

        $response = $this->actingAs($student)->getJson("/lessons/$lesson->id/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $task->id);
    }

    public function test_user_cant_view_task(): void
    {
        $task = Task::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/lessons/{$task->lesson->id}/tasks/{$task->id}");

        $response->assertStatus(403);
    }
}
