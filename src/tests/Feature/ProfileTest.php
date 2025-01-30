<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use App\Models\Address;
use App\Models\Purchase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     */
    public function test_my_page_buy_tab()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $purchasedItem = Item::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id, 'post_number' => '123-4567']);

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
            'address_id' => $address->id
        ]);

        $response = $this->actingAs($user)->get('/mypage?page=buy');

        $response->assertStatus(200);
        $response->assertSee($purchasedItem->name);
        $response->assertDontSee('出品した商品はありません');
    }

    /**
     * 必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
     */
    public function test_my_page_sell_tab()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $listedItem = Item::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/mypage?page=sell');

        $response->assertStatus(200);
        $response->assertSee($listedItem->name);
        $response->assertDontSee('購入した商品はありません');
    }

    /**
     * 変更項目が初期値として過去設定されていること（プロフィール画像、ユーザー名、郵便番号、住所）
     */
    public function test_my_page_initial_address()
    {
        $user = User::factory()
            ->has(Profile::factory()->state([
                'name' => 'テストユーザー',
                'image' => 'test_image.jpg'
            ]))
            ->has(Address::factory()->state([
                'post_number' => '123-4567',
                'address' => '東京都',
                'building' => 'テストビル',
                'is_default' => true
            ]))
            ->create();

        // ✅ `refresh()` でリレーションを再取得
        $user->load('profile', 'addresses');

        $response = $this->actingAs($user)->get(route('mypage.profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('test_image.jpg');
        $response->assertSee('123-4567');
        $response->assertSee('東京都');
        $response->assertSee('テストビル');
    }
}
