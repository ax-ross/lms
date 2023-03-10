<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\AuthorizedRequest;

class PasswordResetRequest extends AuthorizedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email:rfc,dns|max:255',
            'password' => 'required|confirmed|string|min:8'
        ];
    }
}
