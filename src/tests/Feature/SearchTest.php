<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class SearchTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_search_items()
    {
        // テスト用のアイテムを作成
        Item::factory()->create(['name' => '腕時計']);
        Item::factory()->create(['name' => 'HDD']);
        Item::factory()->create(['name' => '玉ねぎ']);

        // 検索クエリを実行
        $response = $this->get('/search?search=腕時計');

        // ステータスコードが200であることを確認
        $response->assertStatus(200);

        // 検索結果に期待するアイテムが含まれていることを確認
        $response->assertSee('腕時計');
        // 検索結果に含まれていないアイテムを確認
        $response->assertDontSee('HDD');
        $response->assertDontSee('玉ねぎ');
    }

    //検索状態がマイリストでも保持される
    public function test_search_items_mylist()
{
    // テスト用のユーザー作成
    $user = User::factory()->create();

    // いいねしたアイテムの作成
    $item = Item::factory()->create(['name' => '腕時計']);
    $user->likedItems()->attach($item->id);

    // その他のアイテムを作成
    Item::factory()->create(['name' => 'HDD']);
    Item::factory()->create(['name' => '玉ねぎ']);

    // マイリストページにアクセス
    $response = $this->actingAs($user)->get('/');

    // ステータスコードが200であることを確認
    $response->assertStatus(200);

    // マイリスト内での検索クエリを実行
    $response = $this->actingAs($user)->get('/search?tab=recommend&search=時計');

    // ステータスコードが200であることを確認
    $response->assertStatus(200);

    // 検索結果に期待するアイテムが含まれていることを確認
    $response->assertSee('腕時計');
    // 検索結果に含まれていないアイテムを確認
    $response->assertDontSee('HDD');
    $response->assertDontSee('玉ねぎ');
}
}