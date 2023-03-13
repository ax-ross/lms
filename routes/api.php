<?php

use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Course\CourseStudentController;
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
    Route::apiResource('courses.students', CourseStudentController::class)->only(['index', 'store', 'destroy']);
});
