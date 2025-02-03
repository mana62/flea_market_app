<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\profile;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //「購入する」ボタンを押下すると購入が完了する
    public function test_purchase()
{
    $user = User::factory()->create();
    $address = Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);
    $item = Item::factory()->create();

    // 「購入する」リクエストを送信
    $response = $this->actingAs($user)->post('/purchase/' . $item->id, [
        'payment_method' => 'card',
    ]);

    // ✅ 購入データが作成されているか確認
    $purchase = Purchase::where('item_id', $item->id)->where('user_id', $user->id)->first();
    $this->assertNotNull($purchase, '購入データが正しく作成されていません。');

    // ✅ リダイレクト確認（`purchase_id` を取得後にチェック！）
    $response->assertRedirect(route('item.payment.page', [
        'item_id' => $item->id,
        'purchase_id' => $purchase->id,
    ]));

    // ✅ 商品が「Sold」となっていることを確認
    $item->refresh();
    $this->assertEquals(1, $item->is_sold, '商品が正しく Sold 状態になっていません。');
}



    //購入済みの商品はsoldと表示
    public function test_show_item_sold()
    {
        //１、テスト用ユーザーの作成
        $user = User::factory()->create();

        //２、アイテム作成
        $item = Item::factory()->create([
            'is_sold' => true,
        ]);

        //３、購入データの作成
        $purchase = Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        //４、トップページを取得
        $response = $this->actingAs($user)->get('/');

        //５、ステータスコードが200であることを確認
        $response->assertStatus(200);

        //６、購入済みアイテムに「sold」が表示されていることを確認
        $response->assertSee('Sold');
    }

    //「プロフィール/購入した商品一覧」に追加されている
    public function test_show_my_page_sold()
    {
        // ユーザーを作成
        $user = User::factory()->create();
    
        // ユーザーのプロフィールを作成
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
        ]);
    
        // アイテムを作成
        $item = Item::factory()->create([
            'user_id' => User::factory()->create()->id, // 出品者を作成
            'name' => 'テスト商品',
        ]);
    
        // 購入データを作成（正しく関連付け）
        $purchase = Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    
        // ユーザーを `with('purchasedItems')` で取得し直す
        $user = User::with('purchasedItems')->find($user->id);
    
        // ユーザーとしてログインし、マイページへアクセス
        $response = $this->actingAs($user)->get('/mypage');
    
        // ステータスコードの確認
        $response->assertStatus(200);
    
        // 購入した商品が表示されていることを確認
        $response->assertSee($item->name); // 購入した商品名が表示されるか
    }
    
}
