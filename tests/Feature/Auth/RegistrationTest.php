<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_new_user(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'Test',
            'email' => 'test@mail.ru',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $this->assertAuthenticated();
        $response->assertStatus(201);
        $this->assertTrue(User::where('email', 'test@mail.ru')->exists());
    }
}
