<?php

namespace Tests\Feature\Message;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_chat_member_can_view_messages()
    {
        $chat = Chat::factory()->has(Message::factory()->count(5))->has(User::factory(), 'users')->create();
        $member = $chat->users->first();
        $user = User::factory()->create();

        $response = $this->actingAs($member)->getJson("/chats/$chat->id/messages");

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }
}
