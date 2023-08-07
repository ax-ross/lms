<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatMemberRequest;
use App\Http\Requests\UpdateChatRequest;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ChatController extends Controller
{

    public function show(Chat $chat): ChatResource
    {
        $this->authorize('view', $chat);
        return new ChatResource($chat);
    }

    public function update(UpdateChatRequest $request, Chat $chat): ChatResource
    {
        $this->authorize('update', $chat);

        $payload = $request->validated();

        $chat->update($payload);

        return new ChatResource($chat->fresh());
    }

    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function addMember(ChatMemberRequest $request, Chat $chat): Response
    {
        $this->authorize('addMember', $chat);

        $user_id = $request->validated()['user_id'];
        if (!$chat->course->students->contains($user_id)) {
            throw ValidationException::withMessages([
               'user_id' => 'only student can be added to the chat',
            ]);
        }

        $chat->users()->attach($user_id);
        return response()->noContent();
    }

    public function removeMember(ChatMemberRequest $request, Chat $chat): Response
    {
        $to_remove = $request->validated()['user_id'];
        $this->authorize('removeMember', [$chat, $to_remove]);

        $chat->users()->detach($to_remove);
        return response()->noContent();
    }
}
