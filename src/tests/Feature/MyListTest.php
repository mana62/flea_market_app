<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    //いいねした商品のみ表示
    public function test_show_likes()
    {
        // テスト用ユーザーの作成
        $user = User::factory()->create();

        // いいねしたアイテムの作成
        $item = Item::factory()->create();
        $user->likedItems()->attach($item->id);

        // 商品一覧ページを取得（クエリパラメータでタブを指定）
        $response = $this->actingAs($user)->get('/?tab=mylist');

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // いいねしたアイテムが表示されていることを確認
        $response->assertSee($item->name);
    }

    //購入済みの商品はsoldと表示
    public function test_show_my_list_sold()
    {
        // テスト用ユーザーの作成
        $user = User::factory()->create();

        // 購入済みアイテムの作成
        $item = Item::factory()->create(['is_sold' => true]);
        $user->likedItems()->attach($item->id);

        // 商品一覧ページを取得（クエリパラメータでタブを指定）
        $response = $this->actingAs($user)->get('/?tab=mylist');

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // 購入済みアイテムに「sold」が表示されていることを確認
        $response->assertSee('Sold');
    }

    //出品した商品は表示されない
    public function test_not_show_my_list_sell()
    {
        // テスト用ユーザーの作成
        $user = User::factory()->create();

        // 自分が出品したアイテムの作成
        $sell = Item::factory()->create(['user_id' => $user->id]);

        // 商品一覧ページを取得（クエリパラメータでタブを指定）
        $response = $this->actingAs($user)->get('/?tab=mylist');

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // 出品した商品が一覧に表示されないことを確認
        $response->assertDontSee($sell->name); 
        }

    //未認証の場合何も表示されない
    public function test_not_show_my_list_guest()
    {
        // 未認証ユーザーとしてマイリストページを取得
    $response = $this->get('/?tab=mylist');

    // ステータスコードが200であることを確認（リダイレクトはしない）
    $response->assertStatus(200);

    // 「いいねした商品はありません」というメッセージが表示されることを確認
    $response->assertSee('いいねした商品はありません');
}
}

