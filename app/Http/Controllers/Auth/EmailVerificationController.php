<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailVerificationController extends Controller
{
    public function verifyEmail(EmailVerificationRequest $request): Response
    {
        $request->fulfill();
        return response()->noContent();
    }

    public function resendNotification(Request $request): Response
    {
        $request->user()->sendEmailVerificationNotification();
        return response()->noContent();
    }
}
