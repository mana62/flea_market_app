<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Profile;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    //「商品名」で部分一致検索ができる
    public function test_search_items()
    {
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        Item::factory()->create(['name' => '腕時計']);

        $response = $this->get('/');
        $response = $this->actingAs($user)->get('/search?tab=recommend&search=時計');
        $response->assertStatus(200);
        $response->assertSee('腕時計');
    }

    //検索状態がマイリストでも保持されている
    public function test_search_items_mylist()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => '腕時計']);
        $user->likedItems()->attach($item->id);
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response = $this->actingAs($user)->get('/search?tab=mylist&search=時計');
        $response->assertStatus(200);
        $response->assertSee('腕時計');
    }
}