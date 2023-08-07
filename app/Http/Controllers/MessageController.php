<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use phpcent\Client as Centrifugo;

class MessageController extends Controller
{
    public function __construct(private readonly Centrifugo $centrifugo)
    {
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Chat $chat): AnonymousResourceCollection
    {
        $this->authorize('view', [Message::class, $chat]);

        return MessageResource::collection($chat->messages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMessageRequest $request, Chat $chat): MessageResource
    {
        $this->authorize('create', [Message::class, $chat]);

        $payload = $request->validated();

        $message = Message::create([
            'user_id' => $request->user()->id,
            'message' => $payload['message'],
            'chat_id' => $chat->id
        ]);

        $channels = [];

        foreach ($chat->users as $user) {
            $channels[] = "personal:user#{$user->id}";
        }

        $send_data = (new MessageResource($message))->toArray($request);

        $this->centrifugo->broadcast($channels, $send_data);

        return new MessageResource($message);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageRequest $request, Chat $chat, Message $message): MessageResource
    {
        $this->authorize('update', [$message, $chat]);

        $payload = $request->validated();
        $message->update($payload);

        return new MessageResource($message->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat, Message $message): Response
    {
        $this->authorize('delete', [$message, $chat]);

        $message->delete();

        return response()->noContent();
    }
}
