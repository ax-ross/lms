<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\CourseInvitation;
use App\Models\User;

class CourseInvitationPolicy
{
    /**
     * Determine whether the user can create the model.
     */
    public function create(User $user, Course $course): bool
    {
        return $user->id === $course->teacher->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CourseInvitation $courseInvitation): bool
    {
        return $user->id === $courseInvitation->course->teacher->user->id;
    }

    /**
     * Determine whether the user can accept the invitation.
     */
    public function accept(User $user, CourseInvitation $courseInvitation): bool
    {
        return $user->id === $courseInvitation->user_id;
    }

    /**
     * Determine whether the user can decline the invitation.
     */
    public function decline(User $user, CourseInvitation $courseInvitation): bool
    {
        return $user->id === $courseInvitation->user_id;
    }
}
