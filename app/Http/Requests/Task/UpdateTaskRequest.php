<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\AuthorizedRequest;

class UpdateTaskRequest extends AuthorizedRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'string|max:255',
            'description' => 'string|max:65535',
            'lesson_id' => 'exists:lessons,id',
            'is_required' => 'boolean',
        ];
    }
}
