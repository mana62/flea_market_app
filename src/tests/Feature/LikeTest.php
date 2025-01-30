<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_likes_items()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // いいねリクエスト
        $response = $this->actingAs($user)->post("/item/{$item->id}/like");

        $response->assertStatus(200);
        $response->assertJson(['liked' => true, 'likesCount' => 1]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $item->refresh();
        $this->assertEquals(1, $item->likedBy()->count());
    }

    public function test_likes_items_color_change()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // いいねリクエスト
        $response = $this->actingAs($user)->post("/item/{$item->id}/like");

        $response->assertStatus(200);
        $response->assertJson(['liked' => true, 'likesCount' => 1]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_likes_items_color_remove()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // いいね
        $this->actingAs($user)->post("/item/{$item->id}/like");

        // いいね解除リクエスト
        $response = $this->actingAs($user)->post("/item/{$item->id}/like");

        $response->assertStatus(200);
        $response->assertJson(['liked' => false, 'likesCount' => 0]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $item->refresh();
        $this->assertEquals(0, $item->likedBy()->count());
    }
}
