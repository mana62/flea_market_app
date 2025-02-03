<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Address;

class ProfileTest extends TestCase
{
    use RefreshDatabase; // データベースをリフレッシュする

    /** @test */
    public function test_profile()
    {
        // テスト用ユーザーを作成
        $user = User::factory()->create([
            'has_profile' => true,
        ]);

        // プロフィール作成
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'name' => 'テストユーザー',
            'image' => 'test_image.jpg',
        ]);

        // 住所作成
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'post_number' => '111-1111',
            'address' => '東京都渋谷区',
            'is_default' => true,
        ]);

        // ユーザーをログインさせる
        $response = $this->actingAs($user)->get(route('mypage.profile.edit'));

        // ページが正常に表示されることを確認
        $response->assertStatus(200);

        // プロフィール画像・ユーザー名・郵便番号・住所が表示されていることを確認
        $response->assertSee(asset('storage/profile_images/test_image.jpg'));
        $response->assertSee('テストユーザー');
        $response->assertSee('111-1111');
        $response->assertSee('東京都渋谷区');
    }
}
