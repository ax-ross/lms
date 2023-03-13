<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Authorize all for course teacher
    */
    public function before(User $user, string $ability, Course $course): ?bool
    {
        return $course->teacher->user->id === $user->id ? true : null;
    }
    
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        return !($course->type === 'private') || $course->students->contains($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can check course students.
     */
    public function viewAnyStudents(User $user, Course $course): bool
    {
        return $course->students->contains($user->id);
    }

    /**
     * Determine whether the user can become student of course.
     */
    public function storeStudent(User $user, Course $course): bool
    {
        return !$course->students->contains($user->id) && $course->type !== 'private';
    }

    /**
     * Determine whether the user can leave course.
     */
    public function deleteStudent(User $user, Course $course, User $beingDeletedStudent): bool
    {
        return $user->id === $beingDeletedStudent->id && $course->students->contains($beingDeletedStudent->id);
    }
}
