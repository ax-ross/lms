<?php

namespace Tests\Feature\Course;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowCourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_course(): void
    {
        $course = Course::factory()->create();
        $teacher = $course->teacher;

        $response = $this->actingAs($teacher)->getJson("/courses/{$course->id}");

        $response->assertStatus(200);
    }

    public function test_student_can_view_course(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();
        $student = $course->students->first();

        $response = $this->actingAs($student)->getJson("/courses/{$course->id}");

        $response->assertStatus(200);
    }

    public function test_user_cant_view_course(): void
    {
        $course = Course::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/courses/{$course->id}");

        $response->assertStatus(403);
    }
}
