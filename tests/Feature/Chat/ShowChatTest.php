<?php

namespace Tests\Feature\Chat;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_chat()
    {
        $chat = Chat::factory()->create();
        $teacher = $chat->course->teacher;

        $response = $this->actingAs($teacher)->getJson("/chats/$chat->id");

        $response->assertStatus(200);

    }

    public function test_member_can_view_chat()
    {
        $chat = Chat::factory()->has(User::factory())->create();
        $member = $chat->users->first();

        $response = $this->actingAs($member)->getJson("/chats/$chat->id");

        $response->assertStatus(200);
    }

    public function test_user_cant_view_chat()
    {
        $chat = Chat::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/chats/$chat->id");

        $response->assertStatus(403);
    }
}
