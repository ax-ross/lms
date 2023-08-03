<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Lesson;
use App\Models\Task;
use App\Services\ImageService;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, Lesson $lesson): TaskResource
    {
        $payload = $request->validated();
        $this->authorize('update', $lesson);
        $images = $request->getImages();

        $task = Task::create($payload);

        //TODO optimize - one query
        $task->images()->saveMany($images);

        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson, Task $task)
    {
        $this->authorize('view', $task);

        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Lesson $lesson, Task $task): TaskResource
    {
        $payload = $request->validated();

        $this->authorize('update', $task);

        $task->update($payload);

        $this->imageService->syncImagesByContent($task, $payload['description']);

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson, Task $task): Response
    {
        $this->authorize('delete', [$task, $lesson]);

        $task->delete();

        return response()->noContent();
    }
}
