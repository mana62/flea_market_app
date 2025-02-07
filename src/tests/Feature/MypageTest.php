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

    //必要な情報が取得できる（プロフィール画像、ユーザー名、出品した商品一覧、購入した商品一覧）
    public function test_mypage_displays_user_info()
    {
        $this->withoutMiddleware('has.profile');

        $user = User::factory()->create([
            'has_profile' => true,
        ]);

        $profile = Profile::factory()->make([
            'name' => 'テストユーザー',
            'image' => 'test_image.jpg',
        ]);

        $user->profile()->save($profile);

        $address = Address::factory()->create([
            'user_id' => $user->id,
            'post_number' => '111-1111',
            'address' => '東京都',
        ]);

        Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品商品',
            'image' => 'sell_image.jpg',
            'is_sold' => false,
        ]);

        $buyItem = Item::factory()->create([
            'name' => '購入商品',
            'image' => 'buy_image.jpg',
            'is_sold' => true,
        ]);

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $buyItem->id,
            'address_id' => $address->id,
            'payment_method' => 'card',
        ]);

        $user = User::with(['purchasedItems', 'listedItems'])->find($user->id);

        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('/storage/profile_images/test_image.jpg');
        $response->assertSee('購入商品');
        $response->assertDontSee('出品商品');

        $response = $this->actingAs($user)->get('/mypage?page=buy');
        $response->assertStatus(200);
        $response->assertSee('購入した商品');
        $response->assertSee('購入商品');
        $response->assertDontSee('出品商品');

        $response = $this->actingAs($user)->get('/mypage?page=sell');
        $response->assertStatus(200);
        $response->assertSee('出品商品');
    }
}
