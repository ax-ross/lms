<?php

namespace App\Http\Requests\Task;

use App\Http\Requests\AuthorizedRequest;
use App\Models\Image;
use Closure;

class StoreTaskRequest extends AuthorizedRequest
{
    private array $images = [];
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:65535',
            'lesson_id' => 'required|exists:lessons,id',
            'imagePaths' => 'array',
            'is_required' => 'required|boolean',
            'imagePaths.*' => ['bail', 'string', function(string $attribute, mixed $value, Closure $fail) {
                if (!$this->images[] = Image::findImageByAbsolutePath($value)) {
                    $fail('Неверный путь до изображения');
                }
            }]
        ];
    }

    public function getImages(): array
    {
        return $this->images;
    }
}
