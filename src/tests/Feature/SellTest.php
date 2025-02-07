<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Profile;

class SellTest extends TestCase
{
    use RefreshDatabase;

    //商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、商品の説明、販売価格）
    public function test_item_listing_success()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('test-image.jpg');
        $imagePath = $image->store('item_images', 'public');

        $data = Item::factory()->create([
            'category' => ['カテゴリ名1', 'カテゴリ名2'],
            'condition' => '良い',
            'name' => 'テスト商品',
            'description' => '商品の説明文',
            'price' => 1000,
            'image' => basename($imagePath),
        ])->toArray();

        $response = $this->actingAs($user)->post(route('item.sell'), $data);
        $response->assertStatus(302);

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'テスト商品',
            'description' => '商品の説明文',
            'price' => 1000,
            'condition' => '良い',
        ]);

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
        ]);

        $item = Item::where('user_id', $user->id)->first();
        $this->assertContains('カテゴリ名1', $item->category);
        $this->assertContains('カテゴリ名2', $item->category);

        $savedItem = Item::first();
        Storage::disk('public')->assertExists('item_images/' . $savedItem->image);
    }

    //出品時のバリデーションチェック
    public function test_item_listing_validation_error()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        $invalidData = [
            'category' => ['カテゴリ名'],
            'condition' => '良い',
            'name' => '',
            'description' => '商品の説明文',
            'price' => 1000,
            'image' => UploadedFile::fake()->image('test-image.jpg'),
        ];

        $response = $this->actingAs($user)->post(route('item.sell'), $invalidData);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name']);
    }
}