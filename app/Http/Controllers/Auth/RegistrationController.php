<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function register(RegistrationRequest $request): UserResource
    {
        $credentials = $request->validated();
        $credentials['password'] = Hash::make($credentials['password']);

        $user = User::create($credentials);

        event(new Registered($user));

        Auth::login($user);

        return new UserResource($user);
    }
}

