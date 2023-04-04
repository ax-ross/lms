<?php

namespace Tests\Feature\CourseSection;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreCourseSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_course_section(): void
    {
        $course = Course::factory()->create();
        $teacher = $course->teacher;

        $response = $this->actingAs($teacher)->postJson("/courses/{$course->id}/sections", [
            'title' => 'test title',
            'description' => 'test description',
            'section_number' => 1
        ]);

        $response->assertStatus(201);
        $this->assertTrue($course->sections()->where('title', 'test title')->exists());
    }

    public function test_teacher_cant_create_course_section_with_invalid_data(): void
    {
        $course = Course::factory()->create();
        $teacher = $course->teacher;

        $response = $this->actingAs($teacher)->postJson("/courses/{$course->id}/sections", [
            'title' => Str::random(256),
            'description' => '',
            'section_number' => 'test'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['title', 'description', 'section_number']]);
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
