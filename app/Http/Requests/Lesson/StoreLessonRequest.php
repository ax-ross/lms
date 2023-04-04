<?php

namespace App\Http\Requests\Lesson;

use App\Http\Requests\AuthorizedRequest;
use App\Models\LessonImage;
use Closure;

class StoreLessonRequest extends AuthorizedRequest
{
    private array $images = [];
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
            'section_id' => 'required|exists:course_sections,id',
            'imagePaths' => 'array',
            'imagePaths.*' => ['bail', 'string', function(string $attribute, mixed $value, Closure $fail) {
                if (!$this->images[] = LessonImage::findImageByAbsolutePath($value)) {
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
