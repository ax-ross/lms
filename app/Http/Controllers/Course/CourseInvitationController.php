<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseInvitationRequest;
use App\Http\Resources\CourseInvitationResource;
use App\Models\Course;
use App\Models\CourseInvitation;
use App\Models\User;
use App\Services\CourseInvitationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CourseInvitationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return CourseInvitationResource::collection($request->user()->courseInvitations);
    }

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException|ValidationException
     */
    public function store(StoreCourseInvitationRequest $request): Response
    {
        $course = Course::find($request->safe()->course_id);

        $this->authorize('create', [CourseInvitation::class, $course]);
        $user = User::where('email', $request->safe()->only('email'))->first();

        CourseInvitation::create(['course_id' => $course->id, 'user_id' => $user->id]);

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     * @throws AuthorizationException
     */
    public function destroy(CourseInvitation $courseInvitation): Response
    {
        $this->authorize('delete', $courseInvitation);
        $courseInvitation->delete();

        return response()->noContent();
    }

    /**
     * @throws AuthorizationException
     */
    public function accept(Request $request, CourseInvitation $courseInvitation): Response
    {
        $this->authorize('accept', $courseInvitation);

        $courseInvitation->course->students()->syncWithoutDetaching($request->user());
        $courseInvitation->delete();

        return response()->noContent();
    }

    /**
     * @throws AuthorizationException
     */
    public function decline(CourseInvitation $courseInvitation): Response
    {
        $this->authorize('decline', $courseInvitation);

        $courseInvitation->delete();

        return response()->noContent();
    }
}
