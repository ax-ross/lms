<?php

namespace Tests\Feature\Course;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreCourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_course(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/courses', [
            'title' => 'course title',
            'description' => 'course description',
            'type' => 'public'
        ]);

        $response->assertStatus(201);
        $this->assertNotNull($course = Course::where('title', 'course title')->first());
        $this->assertNotNull($user->taughtCourses->contains($course));
    }

    public function test_user_cant_create_course_with_invalid_data(): void
    {
        Course::factory()->create(['title' => 'duplicate course title']);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/courses', [
            'title' => 'duplicate course title',
            'type' => 'invalid type'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['title', 'description', 'type']]);
    }
}
