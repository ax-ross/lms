<?php

namespace Tests\Feature\CourseSection;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCourseSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_update_course_section(): void
    {
        $courseSection = CourseSection::factory()->create();
        $course = $courseSection->course;
        $teacher = $course->teacher;

        $payload = [
            'title' => 'updated test title',
            'description' => 'updated test description',
            'section_number' => 1
        ];

        $response = $this->actingAs($teacher)
            ->patchJson("/courses/{$course->id}/sections/{$courseSection->id}", $payload);

        $courseSection = $courseSection->fresh();

        $response->assertStatus(200);
        foreach ($payload as $key => $value) {
            $this->assertTrue($courseSection->$key === $value);
        }
    }

    public function test_teacher_cant_update_course_section_with_invalid_data(): void
    {
        $courseSection = CourseSection::factory()->create();
        $course = $courseSection->course;
        $teacher = $course->teacher;

        $payload = [
            'title' => 123,
            'description' => '',
            'section_number' => 'invalid',
        ];

        $response = $this->actingAs($teacher)
            ->patchJson("/courses/{$course->id}/sections/{$courseSection->id}", $payload);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['title', 'description', 'section_number']]);
    }

    public function test_student_cant_update_course_section(): void
    {
        $course = Course::factory()->has(CourseSection::factory(), 'sections')
            ->has(User::factory(), 'students')->create();
        $student = $course->students->first();
        $courseSection = $course->sections->first();

        $payload = [
            'title' => 'updated test title',
            'description' => 'updated test description',
            'section_number' => 1
        ];

        $response = $this->actingAs($student)
            ->patchJson("/courses/{$course->id}/sections/{$courseSection->id}", $payload);
        $response->assertStatus(403);
    }
}
