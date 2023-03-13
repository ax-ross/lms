<?php

namespace Tests\Feature\Course;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_teacher_can_update_course(): void
    {
        $course = Course::factory()->private()->create();
        $teacher = $course->teacher->user->first();


        $payload = [
            'title' => 'updated course title',
            'description' => 'updated course description',
            'type' => 'public',
        ];

        $response = $this->actingAs($teacher)->patchJson("/courses/{$course->id}", $payload);

        $response->assertStatus(200);

        $course = $course->fresh();

        foreach ($payload as $key => $value) {
            $this->assertTrue($course->$key === $value);
        }
    }

    public function test_non_owner_teacher_cant_update_course(): void
    {
        $course = Course::factory()->public()->create();
        $teacherUser = Teacher::factory()->create()->user;

        $payload = [
            'title' => 'updated course title',
            'description' => 'updated course description',
            'type' => 'public',
        ];

        $response = $this->actingAs($teacherUser)->patchJson("/courses/{$course->id}", $payload);

        $response->assertStatus(403);
    }

    public function test_student_cant_update_course(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();
        $student = $course->students->first();

        $payload = [
            'title' => 'updated course title',
            'description' => 'updated course description',
            'type' => 'public',
        ];

        $response = $this->actingAs($student)->patchJson("/courses/{$course->id}", $payload);
        $response->assertStatus(403);
    }
}
