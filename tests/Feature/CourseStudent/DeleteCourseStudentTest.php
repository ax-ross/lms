<?php

namespace Tests\Feature\CourseStudent;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCourseStudentTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_leave_course(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();
        $student = $course->students->first();

        $response = $this->actingAs($student)->deleteJson("/courses/{$course->id}/students/{$student->id}");

        $response->assertNoContent();
        $this->assertNull($course->students()->find($student->id));
    }

    public function test_teacher_can_remove_student_from_course(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();
        $teacher = $course->teacher;
        $student = $course->students->first();

        $response = $this->actingAs($teacher)->deleteJson("/courses/{$course->id}/students/{$student->id}");

        $response->assertNoContent();
        $this->assertNull($course->students()->find($student->id));
    }

    public function test_cant_remove_not_student_from_course(): void
    {
        $course = Course::factory()->create();
        $teacher = $course->teacher;
        $user = User::factory()->create();

        $response = $this->actingAs($teacher)->deleteJson("/courses/{$course->id}/students/{$user->id}");

        $response->assertStatus(404);
    }
}
