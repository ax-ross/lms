<?php

namespace Tests\Feature\Course;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_delete_course(): void
    {
        $course = Course::factory()->create();
        $teacher = $course->teacher;

        $response = $this->actingAs($teacher)->deleteJson("/courses/{$course->id}");

        $response->assertStatus(204);
        $this->assertModelMissing($course);
    }

    public function test_student_cant_delete_course(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();
        $student = $course->students->first();

        $response = $this->actingAs($student)->deleteJson("/courses/{$course->id}");

        $response->assertStatus(403);
        $this->assertModelExists($course);
    }
}
