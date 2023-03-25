<?php

namespace App\Http\Controllers\Lesson;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lesson\StoreLessonImageRequest;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;

class LessonImageController extends Controller
{
    public function store(StoreLessonImageRequest $request, Lesson $lesson): JsonResponse
    {
        $image = $lesson->images()->create(['path' => $request->file('image')->store('/lesson-images', 'public')]);
        return response()->json([
           'url' => url('storage', $image->path),
        ]);
    }
}
