<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_new_user_and_returns_an_api_token()
    {
        $name = 'John Doe';
        $response = $this->postJson('/api/users', [
            'name' => $name,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['name' => $name]);
        $response->assertJsonStructure(['api_token']);
    }
}
