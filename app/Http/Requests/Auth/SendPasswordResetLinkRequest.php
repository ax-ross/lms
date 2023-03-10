<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\AuthorizedRequest;

class SendPasswordResetLinkRequest extends AuthorizedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc,dns|max:255',
        ];
    }
}
