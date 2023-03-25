<?php

use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Course\CourseInvitationController;
use App\Http\Controllers\Course\CourseSectionController;
use App\Http\Controllers\Course\CourseStudentController;
use App\Http\Controllers\Lesson\LessonController;
use App\Http\Controllers\Lesson\LessonImageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('courses.students', CourseStudentController::class)
        ->only(['index', 'store', 'destroy'])
        ->scoped();

    Route::post("/course-invitations/{course_invitation}/accept", [CourseInvitationController::class, 'accept']);
    Route::delete("/course-invitations/{course_invitation}/decline", [CourseInvitationController::class, 'decline']);
    Route::apiResource('course-invitations', CourseInvitationController::class)
        ->only(['index', 'store', 'destroy']);

    Route::apiResource('courses.sections', CourseSectionController::class)
        ->only(['store', 'update', 'destroy'])
        ->scoped()
        ->middleware('can:update,course');

    Route::apiResource('lessons', LessonController::class);
    Route::post('/lessons/{lesson}/images', [LessonImageController::class, 'store']);
});