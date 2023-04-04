<?php

namespace Tests\Feature\CourseInvitation;

use App\Models\CourseInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeclineCourseInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_invited_user_can_decline_invitation(): void
    {
        $courseInvitation = CourseInvitation::factory()->create();
        $user = $courseInvitation->user;
        $course = $courseInvitation->course;

        $response = $this->actingAs($user)->deleteJson("/course-invitations/{$courseInvitation->id}/decline");

        $response->assertNoContent();
        $this->assertNull($courseInvitation->fresh());
        $this->assertFalse($course->students->contains($user));
    }

    public function test_not_invited_user_cant_decline_course_invitation(): void
    {
        $courseInvitation = CourseInvitation::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/course-invitations/{$courseInvitation->id}/decline");

        $response->assertStatus(403);
    }
}
