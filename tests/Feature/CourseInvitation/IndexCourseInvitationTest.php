<?php

namespace Tests\Feature\CourseInvitation;

use App\Models\CourseInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexCourseInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_his_invitation(): void
    {
        $user = User::factory()->has(CourseInvitation::factory(15))->create();

        $response = $this->actingAs($user)->getJson("/course-invitations");

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
    }
}
