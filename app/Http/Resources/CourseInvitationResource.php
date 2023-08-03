<?php

namespace App\Http\Resources;

use App\Http\Resources\Course\IndexCourseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseInvitationResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'course' => new IndexCourseResource($this->course),
            'created_at' => $this->created_at->diffForHumans()
        ];
    }
}
