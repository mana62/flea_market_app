<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Purchase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;

class PaymentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //小計画面で変更が即時反映される
    public function test_payment_method_selection()
    {
        // 1. ユーザーを作成
        $user = User::factory()->create();

        // 2. アイテムを作成
        $item = Item::factory()->create();

        // 3. ユーザーのデフォルト配送先を作成
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'is_default' => true,
        ]);

        // 4. 購入ページを開く
        $response = $this->actingAs($user)->get('/purchase/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('支払い方法'); // 支払い方法の選択フォームが表示されていることを確認

        // 5. 支払い方法を「コンビニ払い」に変更
        $response = $this->actingAs($user)->post('/purchase/' . $item->id, [
            'payment_method' => 'convenience-store',
        ]);

        // 6. ステータスコードが 200 またはリダイレクト（購入完了ページ）であることを確認
        $response->assertRedirect(route('thanks.buy'));

        // 7. データベースに正しく支払い方法が保存されているか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'convenience-store',
        ]);

        // 8. 購入ページを再度開き、支払い方法が反映されているか確認
        $response = $this->actingAs($user)->get('/purchase/' . $item->id);
        $response->assertStatus(200);
        $response->assertSee('convenience-store'); // 支払い方法が反映されていることを確認
    }
}
