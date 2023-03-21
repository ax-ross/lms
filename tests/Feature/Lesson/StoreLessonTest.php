<?php

namespace Tests\Feature\Lesson;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreLessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_teacher_can_store_lesson(): void
    {
        $section = CourseSection::factory()->create();
        $teacher = $section->course->teacher->user;

        $payload = [
            'title' => 'lesson title',
            'content' => 'lesson content',
            'section_id' => $section->id
        ];

        $response = $this->actingAs($teacher)->postJson('/lessons', $payload);

        $response->assertStatus(201);
        $this->assertTrue($section->lessons->contains('title','lesson title'));
    }

    public function test_non_owner_teacher_cant_store_lesson(): void
    {
        $section = CourseSection::factory()->create();
        $teacher = Teacher::factory()->create()->user;

        $payload = [
            'title' => 'lesson title',
            'content' => 'lesson content',
            'section_id' => $section->id
        ];

        $response = $this->actingAs($teacher)->postJson('/lessons', $payload);

        $response->assertStatus(403);
        $this->assertFalse($section->lessons->contains('title', 'lesson_title'));
    }

    public function test_student_cant_store_lesson(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->has(CourseSection::factory(), 'sections')->create();
        $section = $course->sections->first();
        $student = $course->students->first();

        $payload = [
            'title' => 'lesson title',
            'content' => 'lesson content',
            'section_id' => $section->id
        ];

        $response = $this->actingAs($student)->postJson('/lessons', $payload);

        $response->assertStatus(403);
        $this->assertFalse($section->lessons->contains('title', 'lesson_title'));
    }
}
