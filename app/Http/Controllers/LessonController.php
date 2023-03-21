<?php

namespace App\Http\Controllers;

use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;

class LessonController extends Controller
{
    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(StoreLessonRequest $request): LessonResource
    {
        $payload = $request->validated();
        $this->authorize('create', [Lesson::class, $payload['section_id']]);

        $lesson = Lesson::create($payload);

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

        $this->authorize('update', [$lesson, $payload['section_id']]);

        $lesson->update($payload);

        return new LessonResource($lesson->fresh());
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
