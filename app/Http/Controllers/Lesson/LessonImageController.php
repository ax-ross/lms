<?php

namespace App\Http\Controllers\Lesson;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lesson\StoreLessonImageRequest;
use App\Models\LessonImage;
use Illuminate\Http\JsonResponse;

class LessonImageController extends Controller
{
    public function store(StoreLessonImageRequest $request): JsonResponse
    {
        $image = LessonImage::create(['path' => $request->file('image')->store('/lesson-images', 'public')]);

        return response()->json([
           'url' => url('storage', $image->path),
        ]);
    }
}
