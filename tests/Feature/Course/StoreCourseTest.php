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

        $this->assertNotNull($user->fresh()->teacher);
        $this->assertTrue(Course::where('title', 'course title')->exists());
        $response->assertStatus(201);
    }
}
