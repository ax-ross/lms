<?php

namespace App\Policies;

use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        $course = $lesson->section->course;
        return $user->id === $course->teacher->id || $course->students->contains($user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, int $section_id): bool
    {
        $section = CourseSection::find($section_id);

        return $user->id === $section->course->teacher->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        return $user->id === $lesson->section->course->teacher->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->id === $lesson->section->course->teacher->id;
    }
}
