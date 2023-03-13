<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CourseStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws AuthorizationException
     */
    public function index(Course $course): AnonymousResourceCollection
    {
        $this->authorize('viewAnyStudents', $course);
        return UserResource::collection($course->students);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(Request $request, Course $course): Response
    {
        $this->authorize('storeStudent', $course);
        $user = $request->user();

        $course->students()->attach($user->id);

        return response()->noContent();
    }


    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(Course $course, User $student): Response
    {
        $this->authorize('deleteStudent', [$course, $student]);

        $course->students()->detach($student->id);

        return response()->noContent();
    }
}
