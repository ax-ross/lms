<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        $course = $task->lesson->section->course;

        return $user->id === $course->teacher->id || $course->students->contains($user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Lesson $lesson): bool
    {
        return $user->id === $lesson->section->course->teacher->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->lesson->section->course->teacher->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->lesson->section->course->teacher->id;
    }

}
