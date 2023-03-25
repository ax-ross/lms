<?php

namespace Tests\Feature\LessonImage;

use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoreLessonImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_teacher_can_store_lesson_image()
    {
        Storage::fake('public');

        $lesson = Lesson::factory()->create();
        $teacher = $lesson->section->course->teacher->user;
        $image = UploadedFile::fake()->image('lesson-image.jpg');

        $response = $this->actingAs($teacher)->postJson("/lessons/{$lesson->id}/images", ['image' => $image]);

        $response->assertStatus(200);
        Storage::disk('public')->assertExists('/lesson-images/' . $image->hashName());
    }
}
