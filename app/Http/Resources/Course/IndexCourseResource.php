<?php

namespace App\Http\Resources\Course;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'students_number' => $this->students->count(),
            'teacher' => new UserResource($this->teacher),
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
