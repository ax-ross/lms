<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\AuthorizedRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends AuthorizedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:courses,title',
            'description' => 'required|string|max:65535',
            'type' => ['required', Rule::in(['public', 'private'])]
        ];
    }
}
