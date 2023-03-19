<?php

namespace Tests\Feature\CourseSection;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCourseSectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_teacher_can_delete_course_section(): void
    {
        $courseSection = CourseSection::factory()->create();
        $course = $courseSection->course;
        $teacher = $course->teacher->user;

        $response = $this->actingAs($teacher)->deleteJson("/courses/{$course->id}/sections/$courseSection->id");

        $response->assertNoContent();
        $this->assertFalse(CourseSection::where('title', 'test title')->exists());
    }

    public function test_non_owner_teacher_cant_delete_course_section(): void
    {
        $courseSection = CourseSection::factory()->create();
        $course = $courseSection->course;
        $teacher = Teacher::factory()->create()->user;

        $response = $this->actingAs($teacher)->deleteJson("/courses/{$course->id}/sections/$courseSection->id");

        $response->assertStatus(403);
    }

    public function test_student_cant_delete_course_section(): void
    {
        $course = Course::factory()->has(CourseSection::factory(), 'sections')->has(User::factory(), 'students')->create();
        $student = $course->students->first();
        $courseSection = $course->sections->first();

        $response = $this->actingAs($student)
            ->deleteJson("/courses/{$course->id}/sections/{$courseSection->id}");

        $response->assertStatus(403);
    }
}
