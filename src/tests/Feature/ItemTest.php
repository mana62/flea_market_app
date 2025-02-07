<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    //全商品を取得できる
    public function test_show_item_all()
    {
        $items = Item::factory()->count(10)->create();
        $response = $this->get('/');
        $response->assertStatus(200);

        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    //購入済み商品は「Sold」と表示される
    public function test_show_item_sold()
    {
        $user = User::factory()->create();

        $item = Item::factory()->sold()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'is_sold' => true,
        ]);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertSee('SOLD');
    }

    //自分が出品した商品は表示されない
    public function test_not_show_item_sell()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertDontSee($item->name);
    }
}