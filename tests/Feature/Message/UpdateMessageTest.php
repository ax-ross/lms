<?php

namespace Tests\Feature\Message;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_update_only_his_message()
    {
        $teacher = User::factory()->create();
        $chat = Chat::factory()->has(Message::factory())->recycle($teacher)->create();
        $message = $chat->messages->first();

        $payload = [
            'message' => 'updated message',
        ];

        $response = $this->actingAs($teacher)->patchJson("/chats/$chat->id/messages/$message->id", $payload);

        $response->assertStatus(200);
        $message = $message->fresh();
        foreach ($payload as $key => $value) {
            $this->assertTrue($message->$key === $value);
        }


        $message = Message::factory()->create([
            'chat_id' => $chat->id,
        ]);

        $response = $this->actingAs($teacher)->patchJson("/chats/$chat->id/messages/$message->id", $payload);

        $response->assertStatus(403);
        $message = $message->fresh();
        foreach ($payload as $key => $value) {
            $this->assertFalse($message->$key === $value);
        }
    }

    public function test_member_can_update_only_his_message()
    {
        $chat = Chat::factory()->has(Message::factory())->create();
        $message = $chat->messages->first();
        $member = $message->user;

        $payload = [
            'message' => 'updated message',
        ];

        $response = $this->actingAs($member)->patchJson("/chats/$chat->id/messages/$message->id", $payload);

        $response->assertStatus(200);
        $message = $message->fresh();
        foreach ($payload as $key => $value) {
            $this->assertTrue($message->$key === $value);
        }


        $message = Message::factory()->create([
            'chat_id' => $chat->id,
        ]);

        $response = $this->actingAs($member)->patchJson("/chats/$chat->id/messages/$message->id", $payload);

        $response->assertStatus(403);
        $message = $message->fresh();
        foreach ($payload as $key => $value) {
            $this->assertFalse($message->$key === $value);
        }
    }

    public function test_user_cant_update_message()
    {
        $chat = Chat::factory()->has(Message::factory())->create();
        $message = $chat->messages->first();
        $user = User::factory()->create();

        $payload = [
            'message' => 'updated message',
        ];

        $response = $this->actingAs($user)->patchJson("/chats/$chat->id/messages/$message->id", $payload);

        $response->assertStatus(403);
        $message = $message->fresh();
        foreach ($payload as $key => $value) {
            $this->assertFalse($message->$key === $value);
        }
    }
}
