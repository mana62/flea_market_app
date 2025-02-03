<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use App\Models\Purchase;
use App\Models\Item;

class ChangeAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */

    //送付先住所変更画面にて登録した住所が商品購入画面に反映されている
    public function test_change_address()
{
    $user = User::factory()->create();
    $item = Item::factory()->create();
    
    // 初期の住所を登録
    $oldAddress = Address::factory()->create([
        'user_id' => $user->id,
        'post_number' => '111-1111',
        'address' => '東京都',
        'building' => '',
        'is_default' => true,
    ]);

    // 新しい住所データ
    $newAddressData = [
        'post_number' => '222-2222',
        'address' => '大阪府',
        'building' => 'マンション111',
    ];

    // ✅ 住所変更リクエスト
    $response = $this->actingAs($user)->post(route('change.address', ['item_id' => $item->id]), $newAddressData);
    $response->assertRedirect(route('purchase', ['item_id' => $item->id]));

    // ✅ DB 確認
    $this->assertDatabaseHas('addresses', [
        'user_id' => $user->id,
        'post_number' => '222-2222',
        'address' => '大阪府',
        'building' => 'マンション111',
        'is_default' => true,
    ]);

    // ✅ 購入画面で新しい住所が表示されているか確認
    $response = $this->actingAs($user)->get(route('purchase', ['item_id' => $item->id]));
    $response->assertSee('222-2222');
    $response->assertSee('大阪府');
    $response->assertSee('マンション111');
}
}