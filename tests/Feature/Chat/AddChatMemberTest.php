<?php

namespace Tests\Feature\Chat;

use App\Models\Chat;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddChatMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_teacher_can_add_chat_member()
    {
        $course = Course::factory()->has(User::factory()->count(2), 'students')
            ->has(Chat::factory()->has(User::factory()))->create();
        $chat = $course->chat;
        $users = $course->students;
        $member = $chat->users->first();
        $teacher = $course->teacher;

        $response = $this->actingAs($teacher)->postJson("/chats/{$chat->id}/add", [
            'user_id' => $users[0]->id,
        ]);

        $response->assertStatus(204);
        $chat->load('users');
        $this->assertTrue($chat->users->contains($users[0]->id));

        $response = $this->actingAs($member)->postJson("/chats/{$chat->id}/add", [
            'user_id' => $users[1]->id,
        ]);

        $response->assertStatus(403);
        $chat->load('users');
        $this->assertFalse($chat->users->contains($users[1]->id));
    }

    public function test_only_students_can_be_added_to_chat()
    {
        $chat = Chat::factory()->create();
        $user = User::factory()->create();
        $teacher = $chat->course->teacher;


        $response = $this->actingAs($teacher)->postJson("/chats/$chat->id/add", ['user_id' => $user->id]);

        $response->assertStatus(422);
        $chat->load('users');
        $this->assertFalse($chat->users->contains($user));
    }
}
