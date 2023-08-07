<?php

namespace App\Http\Requests\Message;

use App\Http\Requests\AuthorizedRequest;

class StoreMessageRequest extends AuthorizedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'message' => 'required|string|max:3000'
        ];
    }
}
