<?php

namespace Tests\Feature\Message;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_delete_any_message()
    {
        $chat = Chat::factory()->has(Message::factory())->create();
        $teacher = $chat->course->teacher;
        $message = $chat->messages->first();

        $response = $this->actingAs($teacher)->deleteJson("/chats/$chat->id/messages/$message->id");

        $response->assertStatus(204);
        $this->assertModelMissing($message);
    }




    public function test_member_can_delete_only_his_message()
    {
        $chat = Chat::factory()->has(Message::factory()->count(2))->has(User::factory())->create();
        $message = $chat->messages->first();
        $student = $message->user;

        $response = $this->actingAs($student)->deleteJson("/chats/$chat->id/messages/$message->id");

        $response->assertStatus(204);
        $this->assertModelMissing($message);

        $chat->load('messages');
        $message = $chat->messages->first();
        $student = $chat->users->first();

        $response = $this->actingAs($student)->deleteJson("/chats/$chat->id/messages/$message->id");

        $response->assertStatus(403);
        $this->assertModelExists($message);
    }

    public function test_user_cant_delete_message()
    {
        $chat = Chat::factory()->has(Message::factory())->create();
        $user = User::factory()->create();
        $message = $chat->messages->first();

        $response = $this->actingAs($user)->deleteJson("/chats/$chat->id/messages/$message->id");

        $response->assertStatus(403);
        $this->assertModelExists($message);
    }
}
