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
        // テスト用ユーザーの作成
        $user = User::factory()->create();
    
        // テスト用アイテムの作成
        $item = Item::factory()->create();
    
        // テスト用住所の作成 (最初のデフォルト住所)
        $oldAddress = Address::factory()->create([
            'user_id' => $user->id,
            'post_number' => '111-1111',
            'address' => '東京都',
            'building' => '',
            'is_default' => true, // ← これを `true` にする
        ]);
    
        // 新しい住所データを用意
        $newAddressData = [
            'post_number' => '222-2222',
            'address' => '大阪府',
            'building' => 'マンション111',
        ];
    
        // ログインした状態で配送先変更APIを呼び出し (間違ったルートを修正)
        $response = $this->actingAs($user)->post(route('change.address', ['item_id' => $item->id]), $newAddressData);
    
        // リダイレクト先を確認
        $response->assertRedirect(route('purchase', ['item_id' => $item->id]));
    
        // 新しい住所がデフォルトとして登録されているか確認
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'post_number' => '222-2222',
            'address' => '大阪府',
            'building' => 'マンション111',
            'is_default' => true,
        ]);
    
        // 古い住所がデフォルトではなくなっていることを確認
        $this->assertDatabaseHas('addresses', [
            'id' => $oldAddress->id,
            'is_default' => false,
        ]);
    }
}    