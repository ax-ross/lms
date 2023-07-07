<?php

namespace Tests\Feature\Lesson;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\LessonImage;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateLessonTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_update_lesson(): void
    {
        Storage::fake('public');

        [$lesson, $oldImages] = $this->create_lesson_with_images();
        $teacher = $lesson->section->course->teacher;
        $images = LessonImage::factory()->count(5)->create(['lesson_id' => null]);
        $this->assertTrue(true);
        $payload = $this->generate_payload_for_lesson_update($lesson, $images);


        $response = $this->actingAs($teacher)->patchJson("/lessons/{$lesson->id}", $payload);

        $response->assertStatus(200);
        $this->assertTrue($lesson->fresh()->section_id === $payload['section_id']);

        foreach ($oldImages as $image) {
            $this->assertFalse($lesson->images->contains($image));
        }

        foreach ($images as $image) {
            $this->assertTrue($lesson->images->contains($image));
        }
    }

    public function test_teacher_cant_update_lesson_with_invalid_data(): void
    {
        $lesson = Lesson::factory()->create();
        $teacher = $lesson->section->course->teacher;

        $payload = [
            'title' => 123,
            'content' => '',
            'section_id' => CourseSection::max('id') + 1,
        ];

        $response = $this->actingAs($teacher)->patchJson("/lessons/{$lesson->id}", $payload);

        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'errors' => ['title', 'content', 'section_id']]);
    }

    public function test_student_cant_update_lesson(): void
    {
        $course = Course::factory()
            ->has(User::factory(), 'students')
            ->has(CourseSection::factory()->has(Lesson::factory()), 'sections')
            ->create();

        $lesson = $course->sections->first()->lessons->first();
        $student = $course->students->first();
        $newSection = CourseSection::factory()->create();

        $payload = [
            'title' => 'updated lesson title',
            'content' => 'updated lesson content',
            'section_id' => $newSection->id,
        ];

        $response = $this->actingAs($student)->patchJson("/lessons/{$lesson->id}", $payload);

        $response->assertStatus(403);
        $this->assertFalse($newSection->lessons->contains('title', $payload['title']));
    }

    private function generate_random_html_with_images(array|Collection $images): string
    {
        $content = fake()->randomHtml();
        foreach ($images as $image) {
            $absPath = url('storage', $image->path);
            $content .= " <img src='{$absPath}' alt='image'>";
        }

        return $content;
    }

    private function create_lesson_with_images(): array
    {
        $images = LessonImage::factory()->count(3)->create();
        $content = $this->generate_random_html_with_images($images);

        $lesson = Lesson::factory()->create(['content' => $content]);

        $lesson->images()->saveMany($images);

        return [$lesson, $images];
    }

    private function generate_payload_for_lesson_update(Lesson $lesson, array|Collection $images): array
    {
        $content = $this->generate_random_html_with_images($images);
        $section = CourseSection::factory()->create(['course_id' => $lesson->section->course->id]);

        return [
            'title' => 'updated lesson title',
            'content' => $content,
            'section_id' => $section->id,
        ];
    }
}
