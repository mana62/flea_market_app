<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Address;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    //変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）
    public function test_profile()
    {
        $user = User::factory()->create([
            'has_profile' => true,
        ]);

        Profile::factory()->create([
            'user_id' => $user->id,
            'name' => 'テストユーザー',
            'image' => 'test_image.jpg',
        ]);

        Address::factory()->create([
            'user_id' => $user->id,
            'post_number' => '111-1111',
            'address' => '東京都渋谷区',
            'is_default' => true,
        ]);

        $response = $this->actingAs($user)->get(route('mypage.profile.edit'));
        $response->assertStatus(200);
        $response->assertSee(asset('storage/profile_images/test_image.jpg'));
        $response->assertSee('テストユーザー');
        $response->assertSee('111-1111');
        $response->assertSee('東京都渋谷区');
    }
}
