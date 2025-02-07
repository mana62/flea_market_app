<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    //名前が入力されていない場合、バリデーションメッセージが表示される
    public function test_it_shows_validation_message_if_name_is_not_provided()
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['name' => 'ユーザー名を入力してください']);
    }

    //メールアドレスが入力されていない場合、バリデーションメッセージが表示される
    public function test_it_shows_validation_message_if_email_is_not_provided()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    //パスワードが入力されていない場合、バリデーションメッセージが表示される
    public function test_it_shows_validation_message_if_password_is_not_provided()
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    //パスワードが7文字以下の場合、バリデーションメッセージが表示される
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

    //パスワードが確認用パスワードと一致しない場合、バリデーションメッセージが表示される
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

    //全ての項目が入力されている場合、会員情報が登録され、プロフィール画面に遷移される
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

        $user = User::where('email', 'test@example.com')->first();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect(route('mypage.profile.edit'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
