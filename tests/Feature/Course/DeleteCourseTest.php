<?php

namespace Tests\Feature\Course;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_teacher_can_delete_course(): void
    {
        $course = Course::factory()->private()->create();
        $teacherUser = $course->teacher->user->first();

        $response = $this->actingAs($teacherUser)->deleteJson("/courses/{$course->id}");

        $response->assertStatus(204);

        $this->assertNull(Course::find($course->id));
    }

    public function test_non_owner_teacher_cant_delete_course(): void
    {
        $course = Course::factory()->public()->create();
        $teacherUser = Teacher::factory()->create()->user;
        $response = $this->actingAs($teacherUser)->deleteJson("/courses/{$course->id}");

        $response->assertStatus(403);
    }

    public function test_student_cant_delete_course(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();
        $student = $course->students->first();

        $response = $this->actingAs($student)->deleteJson("/courses/{$course->id}");
        $response->assertStatus(403);
    }
}
