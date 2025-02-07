<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    //メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function test_it_shows_validation_message_if_email_is_not_provided()
    {
        $response = $this->post('/login', [
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    //パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function test_it_shows_validation_message_if_password_is_not_provided()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    //入力情報が間違っている場合、バリデーションメッセージが表示される
    public function test_it_shows_validation_message_if_input_is_wrong()
    {
        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password'
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('ログイン情報が登録されていません', session('errors')->first('email'));
    }

    //正しい情報が入力された場合、ログイン処理が実行される
    public function test_login()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/');
    }
}
