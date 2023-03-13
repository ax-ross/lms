<?php

namespace Tests\Feature\CourseStudent;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreCourseStudentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_become_student_of_public_course(): void
    {
        $course = Course::factory()->public()->create();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson("/courses/{$course->id}/students");

        $response->assertStatus(204);

        $this->assertNotNull($course->students->contains($user));
    }

    public function test_user_cant_become_student_of_private_course(): void
    {
        $course = Course::factory()->private()->create();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson("/courses/{$course->id}/students");

        $response->assertStatus(403);
    }
}
