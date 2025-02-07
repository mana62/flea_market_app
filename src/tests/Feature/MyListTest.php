<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
use App\Models\Like;
use App\Models\Profile;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    //いいねした商品だけが表示される
    public function test_show_likes()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        $item = Item::factory()->create([
            'user_id' => User::factory()->create()->id,
            'name' => 'テスト商品',
        ]);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $user = User::with('likedItems')->find($user->id);
        $response = $this->actingAs($user)->get('/');
        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee($item->name);
    }

    //購入済み商品は「Sold」と表示される
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

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $user = User::with(relations: 'likedItems')->find($user->id);
        $response = $this->actingAs($user)->get('/');
        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSeeText('SOLD');
    }

    //自分が出品した商品は表示されない
    public function test_not_show_my_list()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '出品した商品',
        ]);

        Like::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertDontSee('出品した商品');
    }

    //未認証の場合は何も表示されない
    public function test_not_show_my_list_guest()
    {
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertDontSee('テスト商品');
        $response->assertSee('いいねした商品はありません');
    }
}
