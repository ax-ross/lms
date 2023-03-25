<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonImage;
use DOMDocument;
use Illuminate\Support\Facades\Storage;

final class LessonService
{
    public function store(array $payload, ?array $images): Lesson
    {
        $lesson = Lesson::create($payload);

        foreach ($images as $image) {
            $lesson->images()->save($image);
        }

        return $lesson;
    }

    public function update(Lesson $lesson, array $payload): Lesson
    {
        $lesson->update($payload);

        $imagePaths = $this->getImagesSrcFromHtml($payload['content']);

        $images = [];
        foreach ($imagePaths as $imagePath) {
            $images[] = LessonImage::findImageByAbsolutePath($imagePath);
        }

        Storage::disk('public')->delete(array_column($images, 'path'));
        $lesson->images()->delete($images);
        $lesson->images()->saveMany($images);

        return $lesson->fresh();
    }

    private function getImagesSrcFromHtml(string $content): array
    {
        $imagesSrc = [];
        $contentHtml = new DOMDocument();
        $contentHtml->loadHTML($content);

        $imageEls = $contentHtml->getElementsByTagName('img');

        foreach ($imageEls as $imageEl) {
            $imagesSrc[] = $imageEl->getAttribute('src');
        }

        return $imagesSrc;
    }
}