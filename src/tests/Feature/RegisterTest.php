<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     *
     */

    //名前バリデーション
    public function test_it_shows_validation_message_if_name_is_not_provided()
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors('name');
    }

    //メールアドレス
    public function test_it_shows_validation_message_if_email_is_not_provided()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors('email');
    }

    //パスワード
    public function test_it_shows_validation_message_if_password_is_not_provided()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
        ]);
        $response->assertSessionHasErrors('password');
    }

    //パスワード7文字以下
    public function test_it_shows_validation_message_if_password_is_not_eight()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors('password');
    }

    //パスワードと確認パスワードが一致しない
    public function test_it_shows_validation_message_if_password_is_not_much_to_password_confirmation()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'passwords',
        ]);
        $response->assertSessionHasErrors('password');
    }
}
