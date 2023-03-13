<?php

namespace App\Http\Requests\Course;

use App\Http\Requests\AuthorizedRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends AuthorizedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|max:255|unique:courses,title',
            'description' => 'string|max:65535',
            'type' => Rule::in(['public', 'private'])
        ];
    }
}
