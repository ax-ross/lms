<?php

namespace App\Services;

use App\Models\Contracts\HasImages;
use App\Models\Image;
use DOMDocument;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function
    syncImagesByContent(HasImages $hasImages, string $content): void
    {
        $imagePaths = $this->getImagesSrcFromHtml($content);

        $images = [];
        //TODO optimize - one query
        foreach ($imagePaths as $imagePath) {
            $images[] = Image::findImageByAbsolutePath($imagePath);
        }


        //TODO optimize - move to queue
        Storage::disk('public')->delete(array_column($images, 'path'));

        $hasImages->images()->delete($images);
        $hasImages->images()->saveMany($images);
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