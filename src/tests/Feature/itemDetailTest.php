<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Like;
use App\Models\Comment;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    //必要な情報が表示される（商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、商品情報（カテゴリ、商品の状態）、コメント数、コメントしたユーザー情報、コメント内容）
    public function test_show_item_detail()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'image' => 'test.jpg',
            'category' => 'メンズ',
            'condition' => '良好',
            'name' => '腕時計',
            'description' => '腕時計です',
            'price' => 10000,
            'user_id' => $user->id,
        ]);
        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => '買ってよかったです',
        ]);
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee(asset('storage/item_images/' . $item->image));
        $response->assertSee($item->name);
        $response->assertSee('ブランド名');
        $response->assertSee(number_format($item->price));
        $response->assertSee($item->category);
        $response->assertSee($item->condition);
        $response->assertSee($item->description);
        $response->assertSee('コメント (1)');
        $response->assertSee($comment->user->name);
        $response->assertSee('1');
        $response->assertSee($comment->content);
    }

    //複数選択されたカテゴリが表示されているか
    public function test_show_category()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'category' => implode(',', ['家電', 'キッチン']),
        ]);
        $response = $this->actingAs($user)->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        foreach (explode(',', $item->category) as $category) {
            $response->assertSee($category);
        }
    }
}
