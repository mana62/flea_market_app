<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    //ログアウトができる
    public function test_logout()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $this->assertAuthenticatedAs($user);
        $response = $this->post('/logout');
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}

