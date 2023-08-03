<?php

namespace App\Http\Requests;

class StoreImageRequest extends AuthorizedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'image' => 'required|file|max:10240|mimes:jpg,png,jpeg,webp',
        ];
    }
}
