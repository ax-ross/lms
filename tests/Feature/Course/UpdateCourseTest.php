<?php

namespace Tests\Feature\Course;

use App\Models\Course;
use App\Models\User;
use DragonCode\Support\Facades\Helpers\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_update_course(): void
    {
        $course = Course::factory()->create();
        $teacher = $course->teacher;

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

    public function test_teacher_cant_update_course_with_invalid_data(): void
    {
        $course = Course::factory()->create();
        $teacher = $course->teacher;

        $payload = [
            'title' => Str::random(256),
            'description' => '',
            'type' => 'invalid type',
        ];

        $response = $this->actingAs($teacher)->patchJson("/courses/{$course->id}", $payload);
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['title', 'description', 'type']]);
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
