<?php

namespace Tests\Feature\Lesson;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateLessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_teacher_can_update_lesson(): void
    {
        $lesson = Lesson::factory()->create();
        $teacher = $lesson->section->course->teacher->user;
        $newSection = CourseSection::factory()->create(['course_id' => $lesson->section->course->id]);

        $payload = [
            'title' => 'updated lesson title',
            'content' => 'updated lesson content',
            'section_id' => $newSection->id,
        ];

        $response = $this->actingAs($teacher)->patchJson("/lessons/{$lesson->id}", $payload);

        $response->assertStatus(200);
        $this->assertTrue($newSection->lessons->contains('title', $payload['title']));
    }

    public function test_non_owner_teacher_cant_update_lesson(): void
    {
        $lesson = Lesson::factory()->create();
        $teacher = Teacher::factory()->create()->user;
        $newSection = CourseSection::factory()->create();

        $payload = [
            'title' => 'updated lesson title',
            'content' => 'updated lesson content',
            'section_id' => $newSection->id,
        ];

        $response = $this->actingAs($teacher)->patchJson("/lessons/{$lesson->id}", $payload);

        $response->assertStatus(403);
        $this->assertFalse($newSection->lessons->contains('title', $payload['title']));
    }

    public function test_student_cant_update_lesson(): void
    {
        $course = Course::factory()
            ->has(User::factory(), 'students')
            ->has(CourseSection::factory()->has(Lesson::factory()), 'sections')
            ->create();

        $lesson = $course->sections->first()->lessons->first();
        $student = $course->students->first();
        $newSection = CourseSection::factory()->create();

        $payload = [
            'title' => 'updated lesson title',
            'content' => 'updated lesson content',
            'section_id' => $newSection->id,
        ];

        $response = $this->actingAs($student)->patchJson("/lessons/{$lesson->id}", $payload);

        $response->assertStatus(403);
        $this->assertFalse($newSection->lessons->contains('title', $payload['title']));
    }
}
