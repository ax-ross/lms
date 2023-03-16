<?php

namespace Tests\Feature\CourseInvitation;

use App\Models\Course;
use App\Models\CourseInvitation;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreCourseInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_teacher_can_invite_user_to_a_private_course(): void
    {
        $course = Course::factory()->private()->create();
        $teacher = $course->teacher->user;
        $user = User::factory()->create();

        $response = $this->actingAs($teacher)
            ->postJson("/course-invitations", ['email' => $user->email, 'course_id' => $course->id]);

        $response->assertNoContent();
        $this->assertTrue(CourseInvitation::where(['user_id' => $user->id, 'course_id' => $course->id])->exists());
    }

    public function test_non_owner_teacher_cant_invite_user_to_course(): void
    {
        $course = Course::factory()->public()->create();
        $teacher = Teacher::factory()->create()->user;
        $user = User::factory()->create();

        $response = $this->actingAs($teacher)
            ->postJson("/course-invitations", ['email' => $user->email, 'course_id' => $course->id]);

        $response->assertStatus(403);
    }

    public function test_student_cant_invite_user_to_course(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();
        $student = $course->students->first();
        $user = User::factory()->create();
        $response = $this->actingAs($student)
            ->postJson("/course-invitations", ['email' => $user->email, 'course_id' => $course->id]);

        $response->assertStatus(403);
    }

    public function test_cant_invite_user_to_course_more_then_one_time(): void
    {
        $courseInvitation = CourseInvitation::factory()->create();
        $user = $courseInvitation->user;
        $course = $courseInvitation->course;
        $teacher = $courseInvitation->course->teacher->user;

        $response = $this->actingAs($teacher)
            ->postJson("/course-invitations", ['email' => $user->email, 'course_id' => $course->id]);

        $response->assertStatus(422);
    }

    public function test_cant_invite_user_who_already_course_student(): void
    {
        $course = Course::factory()->has(User::factory(), 'students')->create();
        $student = $course->students->first();
        $teacher = $course->teacher->user;

        $response = $this->actingAs($teacher)
            ->postJson("/course-invitations", ['email' => $student->email, 'course_id' => $course->id]);

        $response->assertStatus(422);
    }
}
