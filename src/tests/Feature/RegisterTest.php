<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_shows_validation_message_if_name_is_not_provided()
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['name' => 'ユーザー名を入力してください']);
    }

    /** @test */
    public function test_it_shows_validation_message_if_email_is_not_provided()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /** @test */
    public function test_it_shows_validation_message_if_password_is_not_provided()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /** @test */
    public function test_it_shows_validation_message_if_password_is_not_eight()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /** @test */
    public function test_it_shows_validation_message_if_password_is_not_much_to_password_confirmation()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'passwords',
        ]);

        $response->assertSessionHasErrors(['password' => '確認用パスワードと一致しません']);
    }

    /** @test */
    public function test_it_registers_user_and_redirects_to_login_when_all_fields_are_provided()
    {
        $response = $this->post('/register', [
            'name' => 'test user',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/email/verify');

        $this->assertDatabaseHas('users', [
            'name' => 'test user',
            'email' => 'test@example.com',
        ]);
    }
}
