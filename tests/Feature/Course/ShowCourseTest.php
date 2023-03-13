<?php

namespace Tests\Feature\Course;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowCourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_teacher_can_view_private_course(): void
    {
        $course = Course::factory()->private()->create();
        $teacherUser = $course->teacher->user->first();


        $response = $this->actingAs($teacherUser)->getJson("/courses/{$course->id}");

        $response->assertStatus(200);
    }

    public function test_non_owner_teacher_cant_view_private_course(): void
    {
        $course = Course::factory()->private()->create();
        $teacherUser = Teacher::factory()->create()->user;
        $response = $this->actingAs($teacherUser)->getJson("/courses/{$course->id}");

        $response->assertStatus(403);
    }

    public function test_course_student_can_view_private_course(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->private()->create();
        $student = $course->students->first();

        $response = $this->actingAs($student)->getJson("/courses/{$course->id}");

        $response->assertStatus(200);
    }

    public function test_non_participant_user_cant_view_private_course(): void
    {
        $course = Course::factory()->private()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/courses/{$course->id}");

        $response->assertStatus(403);
    }
}
