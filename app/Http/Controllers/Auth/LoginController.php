<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request): UserResource|JsonResponse
    {
        if (Auth::attempt($request->safe()->only(['email', 'password']))) {
            $request->session()->regenerate();
            return new UserResource(Auth::user());
        }
        return response()->json([
           'errors' => ['email' => __('auth.failed')]
        ], 401);
    }

    public function getAuthedUser(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request): Response
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
