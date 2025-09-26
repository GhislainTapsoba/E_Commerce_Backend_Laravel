<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class UserFunctionalTest extends TestCase
{
    public function test_it_can_create_user()
    {
        $response = $this->post('/users', [
            'name' => 'Admin Test',
            'email' => 'admin2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => true,
            'role' => 'admin', // Ajout du rôle
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'admin2@example.com']);
    }

    public function test_it_can_show_user()
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }
}
