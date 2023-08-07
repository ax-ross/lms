<?php

use App\Http\Controllers\CentrifugoProxyController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Course\CourseInvitationController;
use App\Http\Controllers\Course\CourseSectionController;
use App\Http\Controllers\Course\CourseStudentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\TaskController;
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

    Route::post('/images', [ImageController::class, 'store']);

    Route::apiResource('lessons.tasks', TaskController::class)
        ->except(['index'])
        ->scoped();

    Route::post('/centrifugo/connect', [CentrifugoProxyController::class, 'connect']);

    Route::apiResource('chats', ChatController::class)->only(['show', 'update']);
    Route::prefix('/chats')->group(function () {
        Route::post('/{chat}/add', [ChatController::class, 'addMember']);
        Route::post('/{chat}/remove', [ChatController::class, 'removeMember']);
        Route::post('/{chat}/ban', [ChatController::class, 'banMember']);
    });

    Route::apiResource('chats.messages', \App\Http\Controllers\MessageController::class)->scoped();
});