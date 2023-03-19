<?php

namespace Tests\Feature\CourseSection;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreCourseSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_teacher_can_create_course_section(): void
    {
        $course = Course::factory()->create();
        $teacher = $course->teacher->user;

        $response = $this->actingAs($teacher)->postJson("/courses/{$course->id}/sections", [
            'title' => 'test title',
            'description' => 'test description',
            'section_number' => 1
        ]);

        $response->assertStatus(201);
        $this->assertTrue(CourseSection::where('title', 'test title')->exists());
    }

    public function test_non_owner_teacher_cant_create_course_section(): void
    {
        $course = Course::factory()->create();
        $teacher = Teacher::factory()->create()->user;

        $response = $this->actingAs($teacher)->postJson("/courses/{$course->id}/sections", [
            'title' => 'test title',
            'description' => 'test description',
            'section_number' => 1
        ]);

        $response->assertStatus(403);
    }

    public function test_student_cant_create_course_section(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();
        $student = $course->students->first();

        $response = $this->actingAs($student)->postJson("/courses/{$course->id}/sections", [
            'title' => 'test title',
            'description' => 'test description',
            'section_number' => 1
        ]);

        $response->assertStatus(403);
    }
}
