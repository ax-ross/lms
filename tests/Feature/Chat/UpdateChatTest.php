<?php

namespace Tests\Feature\Chat;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_update_chat()
    {
        $chat = Chat::factory()->create();
        $teacher = $chat->course->teacher;

        $payload = ['title' => 'new chat title'];

        $response = $this->actingAs($teacher)->patchJson("/chats/{$chat->id}", $payload);

        $response->assertStatus(200);

        $this->assertTrue($payload['title'] === $chat->fresh()->title);
    }

    public function test_teacher_cant_update_chat_with_invalid_data()
    {
        $chat = Chat::factory()->create();
        $teacher = $chat->course->teacher;

        $payload = ['title' => 123];

        $response = $this->actingAs($teacher)->patchJson("/chats/{$chat->id}", $payload);

        $response->assertStatus(422);

        $this->assertFalse($payload['title'] === $chat->fresh()->title);
    }
    
    public function test_member_cant_update_chat()
    {
        $chat = Chat::factory()->has(User::factory())->create();
        $user = $chat->users->first();

        $payload = ['title' => 123];

        $response = $this->actingAs($user)->patchJson("/chats/{$chat->id}", $payload);

        $response->assertStatus(422);

        $this->assertFalse($payload['title'] === $chat->fresh()->title);
    }
}
