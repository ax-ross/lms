<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\AuthorizedRequest;

class RegistrationRequest extends AuthorizedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|max:255|unique:users',
            'password' => 'required|confirmed|string|min:8',
        ];
    }
}
