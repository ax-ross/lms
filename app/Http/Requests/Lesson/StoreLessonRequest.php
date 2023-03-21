<?php

namespace App\Http\Requests\Lesson;

use App\Http\Requests\AuthorizedRequest;

class StoreLessonRequest extends AuthorizedRequest
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
            'content' => 'required|string|max:65535',
            'section_id' => 'required|exists:course_sections,id'
        ];
    }
}
