<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use App\Services\ImageService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;

class LessonController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(StoreLessonRequest $request): LessonResource
    {
        $payload = $request->validated();
        $this->authorize('create', [Lesson::class, $payload['section_id']]);
        $images = $request->getImages();

        $lesson = Lesson::create($payload);

        //TODO optimize - one query
        $lesson->images()->saveMany($images);

        return new LessonResource($lesson);
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(Lesson $lesson): LessonResource
    {
        $this->authorize('view', $lesson);

        return new LessonResource($lesson);
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateLessonRequest $request, Lesson $lesson): LessonResource
    {
        $payload = $request->validated();

        $this->authorize('update', $lesson);

        $lesson->update($payload);

        $this->imageService->syncImagesByContent($lesson, $payload['content']);

        return new LessonResource($lesson);
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Lesson $lesson): Response
    {
        $this->authorize('delete', $lesson);

        $lesson->delete();

        return response()->noContent();
    }
}
