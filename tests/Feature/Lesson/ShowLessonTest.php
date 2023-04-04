<?php

namespace Tests\Feature\Lesson;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowLessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_lesson(): void
    {
        $lesson = Lesson::factory()->create();
        $teacher = $lesson->section->course->teacher;

        $response = $this->actingAs($teacher)->getJson("/lessons/$lesson->id");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $lesson->id);
    }

    public function test_student_can_view_lesson(): void
    {
        $course = Course::factory()
            ->has(User::factory(), 'students')
            ->has(CourseSection::factory()->has(Lesson::factory()), 'sections')
            ->create();

        $lesson = $course->sections->first()->lessons->first();
        $student = $course->students->first();

        $response = $this->actingAs($student)->getJson("/lessons/$lesson->id");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $lesson->id);
    }

    public function test_user_cant_view_lesson(): void
    {
        $lesson = Lesson::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/lessons/$lesson->id");

        $response->assertStatus(403);
    }
}
