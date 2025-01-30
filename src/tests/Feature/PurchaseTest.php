<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_purchase()
    {
        // テスト用ユーザーの作成
        $user = User::factory()->create();

        // テスト用アイテムの作成
        $item = Item::factory()->create();

        // 購入データの作成
        $purchase = Purchase::factory()->create([
            'item_id' => $user->id,  
            'profile_id' => '3',
            'payment_method' => ['card'],
            'post_number' => '111-1111',
            'address' => '東京都',
            'building' => '', 
        ]);

        // 購入ページにアクセスし、購入処理を実行
        $response = $this->actingAs($user)->post('/item_purchase', [
            'item_id' => $item->id, 
        ]);

        // ステータスコードの確認
        $response->assertStatus(200);

        // 購入がデータベースに保存されているかを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    //購入済みの商品はsoldと表示
    public function test_show_item_sold()
    {
        // テスト用ユーザーの作成
        $user = User::factory()->create();
        
        // 購入済みアイテムの作成
        $item = Item::factory()->create();

        // 購入データの作成
        $purchase = Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // トップページを取得
        $response = $this->actingAs($user)->get('/');

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // 購入済みアイテムに「sold」が表示されていることを確認
        $response->assertSee('Sold');
    }

    //「プロフィール/購入した商品一覧」に追加されている
    public function test_show_my_page_sold()
    {
        // テスト用ユーザーの作成
        $user = User::factory()->create();

        // テスト用アイテムの作成
        $item = Item::factory()->create();

        // 購入データの作成
        $purchase = Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // プロフィール画面にアクセス
        $response = $this->actingAs($user)->get('/mypage?page=buy');

        // ステータスコードの確認
        $response->assertStatus(200);

        // 購入した商品が表示されていることを確認
        $response->assertSee($item->name); // 例: 購入したアイテム名が表示される
    }
}
