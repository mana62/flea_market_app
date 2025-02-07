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

    //「購入する」ボタンを押下すると購入が完了する
    public function test_purchase()
    {
        $user = User::factory()->create();

        Address::factory()->create(['user_id' => $user->id, 'is_default' => true]);

        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post('/purchase/' . $item->id, [
            'payment_method' => 'card',
        ]);

        $purchase = Purchase::where('item_id', $item->id)->where('user_id', $user->id)->first();

        $this->assertNotNull($purchase, 'データが正しく作成されていません');

        $response->assertRedirect(route('item.payment.page', [
            'item_id' => $item->id,
            'purchase_id' => $purchase->id,
        ]));

        $item->refresh();
        $this->assertEquals(1, $item->is_sold, 'SOLDではありません');
    }



    //購入した商品は商品一覧画面にて「sold」と表示される
    public function test_show_item_sold()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        $item = Item::factory()->create([
            'user_id' => User::factory()->create()->id,
            'name' => 'テスト商品',
            'is_sold' => true,
        ]);

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(status: 200);
        $response->assertSee('SOLD', false);
    }


    //「プロフィール/購入した商品一覧」に追加されている
    public function test_show_my_page_buy()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        $item = Item::factory()->create([
            'user_id' => User::factory()->create()->id,
            'name' => 'テスト商品',
        ]);

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/mypage');
        $user = User::with('purchasedItems')->find($user->id);
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee($item->name);
    }
}