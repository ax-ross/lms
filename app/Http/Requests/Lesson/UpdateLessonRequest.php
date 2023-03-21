<?php

namespace App\Http\Requests\Lesson;

use App\Http\Requests\AuthorizedRequest;

class UpdateLessonRequest extends AuthorizedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'string|max:255',
            'content' => 'string|max:65535',
            'section_id' => 'exists:course_sections,id'
        ];
    }
}
