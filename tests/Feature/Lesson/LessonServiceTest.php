<?php

namespace Tests\Feature\Lesson;

use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\LessonImage;
use App\Services\LessonService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LessonServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_course()
    {
        $section = CourseSection::factory()->create();
        $images = $this->create_images(3);
        $payload = [
            'title' => 'test title',
            'content' => 'test content',
            'section_id' => $section->id,
        ];

        $courseService = new LessonService();
        $lesson = $courseService->store($payload, $images);

        $this->assertModelExists($lesson);
        foreach ($images as $image) {
            $this->assertTrue($lesson->images->contains($image));
        }
    }

    public function test_update_course()
    {
        $lesson = Lesson::factory()->has(LessonImage::factory(5), 'images')->create();
        $previousImages = $lesson->images;
        $section = CourseSection::factory()->create();
        $images = $this->create_images(3);
        $payload = [
            'title' => 'updated title',
            'content' => $this->generate_random_html_with_images($images),
            'section_id' => $section->id,
        ];

        $courseService = new LessonService();
        $lesson = $courseService->update($lesson, $payload);

        $this->assertTrue($section->lessons()->where('title', 'updated title')->exists());
        foreach ($images as $image) {
            $this->assertTrue($lesson->images->contains($image));
        }
        foreach ($previousImages as $image) {
            $this->assertFalse($lesson->images->contains($image));
        }

    }

    private function create_images($count): array
    {
        $images = [];

        for ($i = 0; $i < $count; $i++) {
            $images[] = LessonImage::create(['path' => UploadedFile::fake()->image('test-image2')
                ->store('/lesson-images', 'public')]);
        }

        return $images;
    }

    private function generate_random_html_with_images($images): string
    {
        $content = fake()->randomHtml();
        foreach ($images as $image) {
            $absPath = url('storage', $image->path);
            $content .= " <img src='{$absPath}' alt='image'>";
        }

        return $content;
    }
}
