<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class ItemTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //全アイテム取得
    public function test_show_item_all()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    //購入済みの商品はsoldと表示
    public function test_show_item_sold()
    {
        // テスト用ユーザーの作成
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'is_sold' => true,
        ]);

        // トップページを取得
        $response = $this->actingAs($user)->get('/'); // actingAs=特定のユーザーとして動作することをシミュレート(認証済みユーザーとしてのテストが可能)
        // ステータスコードが200であることを確認
        $response->assertStatus(200); // assertStatus=HTTPレスポンスのステータスコードを確認

        // 購入済みアイテムに「sold」が表示されていることを確認
        $response->assertSee('Sold'); // assertSee=HTMLレスポンスの中に特定の文字列が含まれていることを確認
    }

    //出品した商品は表示されない
    public function test_not_show_item_sell()
    {
        // テスト用ユーザーの作成
        $user = User::factory()->create();

        // 自分が出品したアイテムの作成
        $item = Item::factory()->create(['user_id' => $user->id]);

        // トップページを取得
        $response = $this->actingAs($user)->get('/'); // actingAs=特定のユーザーとして動作することをシミュレート(認証済みユーザーとしてのテストが可能)

        // ステータスコードが200であることを確認
        $response->assertStatus(200); // assertStatus=HTTPレスポンスのステータスコードを確認
        $response->assertDontSee($item->name); 

    }
}