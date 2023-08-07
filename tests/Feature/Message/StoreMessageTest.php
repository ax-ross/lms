<?php

namespace Tests\Feature\Message;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_chat_member_can_store_message()
    {
        $chat = Chat::factory()->has(User::factory(), 'users')->create();
        $member = $chat->users()->first();
        $user = User::factory()->create();

        $response = $this->actingAs($member)->postJson("/chats/$chat->id/messages", [
            'message' => 'test message',
        ]);

        $response->assertStatus(201);
        $this->assertModelExists(Message::where([
            ['message', 'test message'],
            ['user_id', $member->id],
        ])->first());

        $response = $this->actingAs($user)->postJson("/chats/$chat->id/messages", [
            'message' => 'test message',
        ]);

        $response->assertStatus(403);
        $this->assertNull(Message::where([
            ['message', 'test message'],
            ['user_id', 5],
        ])->first());
    }
}
