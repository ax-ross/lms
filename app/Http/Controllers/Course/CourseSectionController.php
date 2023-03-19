<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseSection\StoreCourseSectionRequest;
use App\Http\Requests\CourseSection\UpdateCourseSectionRequest;
use App\Http\Resources\CourseSectionResource;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\Response;

class CourseSectionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseSectionRequest $request, Course $course): CourseSectionResource
    {
        $payload = $request->validated();
        $courseSection = $course->sections()->create($payload);

        return new CourseSectionResource($courseSection);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseSectionRequest $request, Course $course, CourseSection $section): CourseSectionResource
    {
        $payload = $request->validated();
        $section->update($payload);

        return new CourseSectionResource($section->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, CourseSection $section): Response
    {
        $section->delete();

        return response()->noContent();
    }
}
