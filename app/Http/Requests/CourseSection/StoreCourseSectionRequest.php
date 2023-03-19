<?php

namespace App\Http\Requests\CourseSection;

use App\Http\Requests\AuthorizedRequest;

class StoreCourseSectionRequest extends AuthorizedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
            'section_number' => 'required|integer'
        ];
    }
}
