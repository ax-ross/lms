<?php

namespace Tests\Feature\CourseStudent;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexCourseStudentTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_course_participants(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();
        $student = $course->students->first();

        $response = $this->actingAs($student)->getJson("/courses/{$course->id}/students");

        $response->assertStatus(200);
    }

    public function test_non_participant_user_cant_view_course_participants(): void
    {
        $course = Course::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/courses/{$course->id}/students");

        $response->assertStatus(403);
    }
}
