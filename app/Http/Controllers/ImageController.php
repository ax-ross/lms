<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Models\Image;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    public function store(StoreImageRequest $request): JsonResponse
    {
        $image = Image::create(['path' => $request->file('image')->store('/images', 'public')]);

        return response()->json([
           'url' => url('storage', $image->path),
        ]);
    }
}
