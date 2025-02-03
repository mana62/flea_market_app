<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Address;

class MypageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_mypage_displays_correct_user_info()
    {
        $this->withoutMiddleware('has.profile');

        // ユーザー作成
        $user = User::factory()->create([
            'has_profile' => true,
        ]);

        // プロフィール作成
        $profile = Profile::factory()->make([
            'name' => 'テストユーザー',
            'image' => 'test_image.jpg',
        ]);
        $user->profile()->save($profile);

        // 住所作成
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'post_number' => '111-1111',
            'address' => '東京都',
        ]);

        // 出品した商品を作成
        $sellItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品商品',
            'image' => 'sell_image.jpg',
            'is_sold' => false,
        ]);

        // 購入した商品を作成
        $buyItem = Item::factory()->create([
            'name' => '購入商品',
            'image' => 'buy_image.jpg',
            'is_sold' => true,
        ]);

        // 購入データを作成
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'item_id' => $buyItem->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        // ユーザーを再取得（関連データを正しく読み込むため）
        $user = User::with(['purchasedItems', 'listedItems'])->find($user->id);

        // マイページ（デフォルト：購入商品）を取得
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee(asset('storage/profile_images/test_image.jpg'));
        $response->assertSee('購入した商品');
        $response->assertSee('購入商品'); // 購入商品の表示確認
        $response->assertDontSee('出品商品'); // 出品商品はデフォルトでは表示されない

        // タブごとのテスト（購入商品）
        $response = $this->actingAs($user)->get('/mypage?page=buy');
        $response->assertStatus(200);
        $response->assertSee('購入した商品');
        $response->assertSee('購入商品'); // 購入商品の表示確認
        $response->assertDontSee('出品商品'); // 出品商品は表示されない

        // タブごとのテスト（出品商品）
        $response = $this->actingAs($user)->get('/mypage?page=sell');
        $response->assertStatus(200);
        $response->assertSee('出品した商品');
        $response->assertSee('出品商品'); // 出品商品の表示確認
        $response->assertDontSee('購入商品'); // 購入商品は表示されない
    }
}
