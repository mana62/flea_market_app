<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;

class PaymentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_payment_method_selection()
    {
        // テスト用ユーザーの作成
        $user = User::factory()->create();
        
        // テスト用アイテムの作成
        $item = Item::factory()->create();

        // 初期の購入データの作成
        $purchase = Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => '',
        ]);

        // 支払い方法選択ページにアクセス
$response = $this->actingAs($user)->get('/purchase/' . $item->id);

        // ステータスコードの確認
        $response->assertStatus(200);

        // プルダウンメニューから支払い方法を選択して更新
$response = $this->actingAs($user)->post('/purchase/' . $item->id, [
    'payment_method' => 'convenience-store', // 新しい支払い方法
]);
        // ステータスコードの確認
        $response->assertStatus(200);

        // 支払い方法が正しくデータベースに保存されているかを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'convenience-store', // 新しい支払い方法が反映されているか確認
        ]);
    }
}