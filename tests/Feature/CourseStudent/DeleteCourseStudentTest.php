<?php

namespace Tests\Feature\CourseStudent;

use App\Models\Course;
use App\Models\Teacher;
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

    public function test_owner_teacher_can_remove_student_from_course(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();

        $teacher = $course->teacher->user;
        $student = $course->students->first();


        $response = $this->actingAs($teacher)->deleteJson("/courses/{$course->id}/students/{$student->id}");

        $response->assertNoContent();
        $this->assertNull($course->students()->find($student->id));
    }

    public function test_non_owner_teacher_cant_remove_student_from_course(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();

        $teacher = Teacher::factory()->create()->user;
        $student = $course->students->first();


        $response = $this->actingAs($teacher)->deleteJson("/courses/{$course->id}/students/{$student->id}");

        $response->assertStatus(403);
    }

    public function test_teacher_cant_remove_non_student_user(): void
    {
        $course = Course::factory()->create();
        $teacher = $course->teacher->user;
        $user = User::factory()->create();

        $response = $this->actingAs($teacher)->deleteJson("/courses/{$course->id}/students/{$user->id}");
        $response->assertStatus(404);
    }
}
