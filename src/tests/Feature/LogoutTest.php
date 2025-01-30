<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_logout()
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

        $this->assertAuthenticatedAs($user); //assertAuthenticatedAs=ログイン後に特定のユーザーが正しく認証されたかどうかを確認するための関数

        // ログアウトリクエストを送信
        $response = $this->post('/logout');

        // セッションにエラーがないことを確認
        $response->assertSessionHasNoErrors(); //assertSessionHasNoErrors=セッションにエラーメッセージが存在しないことを確認

        // ログアウト後にリダイレクトされるURLを確認
        $response->assertRedirect('/login'); //assertRedirect=指定されたURLにリダイレクトされたことを確認

        // ユーザーがログアウトされたことを確認
        $this->assertGuest(); //assertGuest=ログアウト後にユーザーが認証されていない（ゲストとして扱われる）ことを確認する
    }
}

