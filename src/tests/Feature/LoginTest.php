<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * メールアドレスが入力されていない場合のバリデーションメッセージ
     *
     * @test
     */
    public function test_it_shows_validation_message_if_email_is_not_provided()
    {
        $response = $this->post('/login', [
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * パスワードが入力されていない場合のバリデーションメッセージ
     *
     * @test
     */
    public function test_it_shows_validation_message_if_password_is_not_provided()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * 入力情報が間違っている場合のバリデーションメッセージ
     *
     * @test
     */
    public function test_it_shows_validation_message_if_input_is_wrong()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password'
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * 全て正しい場合のログイン成功
     *
     * @test
     */
    public function test_login()
    {
        // 事前にユーザーを作成
        $user = \App\Models\User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // bcryptを使用してパスワードをハッシュ化
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/'); // ログイン後にリダイレクトされるURLを指定
    }
}
