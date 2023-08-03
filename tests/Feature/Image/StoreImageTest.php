<?php

namespace Tests\Feature\Image;

use App\Models\Lesson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoreImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_store_lesson_image()
    {
        Storage::fake('public');

        $lesson = Lesson::factory()->create();
        $teacher = $lesson->section->course->teacher;
        $image = UploadedFile::fake()->image('lesson-image.jpg');

        $response = $this->actingAs($teacher)->postJson("/images", ['image' => $image]);

        $response->assertStatus(200);
        Storage::disk('public')->assertExists('/images/' . $image->hashName());
    }
}
