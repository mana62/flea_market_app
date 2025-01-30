<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    // 商品詳細ページの表示確認
    public function test_show_item_detail()
    {
        $item = Item::factory()->create();

        // ルートを修正！
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));

        $response->assertStatus(200);
    }

    // カテゴリが表示されているか
    public function test_show_category()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'category' => implode(',', ['家電', 'キッチン']), // ✅ カンマ区切りで保存
        ]);

        //ログインした状態で詳細ページへアクセス
        $response = $this->actingAs($user)->get(route('item.detail', ['item_id' => $item->id]));

        $response->assertStatus(200);

        //カテゴリ名がページに表示されているか確認
        foreach (explode(',', $item->category) as $category) {
            $response->assertSee($category);
        }
    }
}
