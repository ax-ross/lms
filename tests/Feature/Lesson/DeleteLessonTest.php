<?php

namespace Tests\Feature\Lesson;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteLessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_teacher_can_delete_lesson(): void
    {
        $lesson = Lesson::factory()->create();
        $teacher = $lesson->section->course->teacher->user;

        $response = $this->actingAs($teacher)->deleteJson("/lessons/$lesson->id");

        $response->assertNoContent();
        $this->assertNull($lesson->fresh());
    }

    public function test_non_owner_teacher_cant_delete_lesson(): void
    {
        $lesson = Lesson::factory()->create();
        $teacher = Teacher::factory()->create()->user;

        $response = $this->actingAs($teacher)->deleteJson("/lessons/$lesson->id");

        $response->assertStatus(403);
        $this->assertNotNull($lesson);
    }

    public function test_student_cant_delete_lesson(): void
    {
        $course = Course::factory()
            ->has(User::factory(), 'students')
            ->has(CourseSection::factory()->has(Lesson::factory()), 'sections')
            ->create();

        $lesson = $course->sections->first()->lessons->first();
        $student = $course->students->first();

        $response = $this->actingAs($student)->deleteJson("/lessons/$lesson->id");

        $response->assertStatus(403);
        $this->assertNotNull($lesson);
    }
}
