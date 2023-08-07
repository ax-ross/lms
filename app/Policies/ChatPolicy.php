<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChatPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Chat $chat): bool
    {
        return $chat->users->contains($user) || $chat->course->teacher->id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Chat $chat): bool
    {
        return $chat->course->teacher->id === $user->id;
    }

    public function addMember(User $user, Chat $chat): bool
    {
        return $chat->course->teacher->id === $user->id;
    }

    public function removeMember(User $user, Chat $chat, int $to_remove): bool
    {
        return $user->id === $to_remove || $chat->course->teacher->id === $user->id;
    }
}
