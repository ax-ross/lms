<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'guest'])->group(function () {
    Route::post('/register', [RegistrationController::class, 'register']);

    Route::post('/login', [LoginController::class, 'login']);

    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
        ->name('password.email');

    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('password.update');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [LoginController::class, 'getAuthedUser']);

    Route::post('/logout', [LoginController::class, 'logout']);

    Route::middleware('signed')
        ->get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resendNotification'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});