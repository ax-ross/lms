<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\Course\StoreCourseRequest;
use App\Http\Requests\Course\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        return CourseResource::collection(Course::public()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request): CourseResource
    {
        $validatedPayload = $request->validated();
        $user = $request->user();

        $course = $user->taughtCourses()->create($validatedPayload);

        return new CourseResource($course);
    }

    /**
     * Display the specified resource.
     * @throws AuthorizationException
     */
    public function show(Course $course): CourseResource
    {
        $this->authorize('view', $course);
        return new CourseResource($course);
    }

    /**
     * Update the specified resource in storage.
     * @throws AuthorizationException
     */
    public function update(UpdateCourseRequest $request, Course $course): CourseResource
    {
        $this->authorize('update', $course);

        $validatedPayload = $request->validated();
        $course->update($validatedPayload);
        return new CourseResource($course->fresh());
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Course $course): Response
    {
        $this->authorize('delete', $course);
        $course->delete();
        return response()->noContent();
    }
}
