<?php

namespace Tests\Feature\CourseInvitation;

use App\Models\CourseInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcceptCourseInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invited_user_can_accept_course_invitation(): void
    {
        $courseInvitation = CourseInvitation::factory()->create();
        $user = $courseInvitation->user;
        $course = $courseInvitation->course;

        $response = $this->actingAs($user)->postJson("/course-invitations/{$courseInvitation->id}/accept");

        $response->assertNoContent();
        $this->assertNull($courseInvitation->fresh());
        $this->assertTrue($course->students->contains($user));
    }

    public function test_user_cant_accept_not_his_own_invitation(): void
    {
        $courseInvitation = CourseInvitation::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson("/course-invitations/{$courseInvitation->id}/accept");

        $response->assertStatus(403);
    }
}
