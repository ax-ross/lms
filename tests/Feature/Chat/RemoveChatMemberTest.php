<?php

namespace Tests\Feature\Chat;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveChatMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_teacher_can_remove_chat_member()
    {
        $chat = Chat::factory()->has(User::factory()->count(3))->create();
        $teacher = $chat->course->teacher;
        $members = $chat->users;

        $response = $this->actingAs($teacher)->postJson("/chats/$chat->id/remove", ['user_id' => $members[0]->id]);

        $response->assertStatus(204);
        $chat->load('users');
        $this->assertFalse($chat->users->contains($members[0]));

        $response = $this->actingAs($members[1])->postJson("/chats/$chat->id/remove", ['user_id' => $members[2]->id]);

        $response->assertStatus(403);
        $chat->load('users');
        $this->assertTrue($chat->users->contains($members[2]));
    }

    public function test_member_can_leave_chat()
    {
        $chat = Chat::factory()->has(User::factory())->create();
        $member = $chat->users->first();

        $response = $this->actingAs($member)->postJson("/chats/$chat->id/remove", ['user_id' => $member->id]);

        $response->assertStatus(204);
        $chat->load('users');
        $this->assertFalse($chat->users->contains($member));
    }
}
