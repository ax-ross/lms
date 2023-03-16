<?php

namespace Tests\Feature\CourseInvitation;

use App\Models\CourseInvitation;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCourseInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_teacher_can_delete_course_invitation(): void
    {
        $courseInvitation = CourseInvitation::factory()->create();
        $teacher = $courseInvitation->course->teacher->user;

        $response = $this->actingAs($teacher)->deleteJson("/course-invitations/{$courseInvitation->id}");

        $response->assertNoContent();
        $this->assertNull($courseInvitation->fresh());
    }

    public function test_non_owner_teacher_cant_delete_course_invitation(): void
    {
        $courseInvitation = CourseInvitation::factory()->create();
        $teacher = Teacher::factory()->create()->user;

        $response = $this->actingAs($teacher)->deleteJson("/course-invitations/{$courseInvitation->id}");

        $response->assertStatus(403);
    }

    public function test_invited_user_cant_delete_course_invitation(): void
    {
        $courseInvitation = CourseInvitation::factory()->create();
        $user = $courseInvitation->user;

        $response = $this->actingAs($user)->deleteJson("/course-invitations/{$courseInvitation->id}");

        $response->assertStatus(403);
    }
}
