<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class SellTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 商品出品機能のテスト
     */
    public function test_item_listing()
    {
        // ✅ ストレージの仮設定
        Storage::fake('public');

        // ✅ ユーザー作成 & ログイン
        $user = User::factory()->create();

        // ✅ 商品画像のダミーファイル
        $image = UploadedFile::fake()->image('test-image.jpg');

        // ✅ 商品出品データ
        $data = [
            'category' => ['カテゴリ名'],
            'condition' => '商品の状態',
            'name' => '商品名',
            'description' => '商品の説明',
            'price' => 1000,
            'img' => $image, // 画像ファイルを送信
        ];

        // ✅ 出品処理を実行
        $response = $this->actingAs($user)->post(route('item.sell'), $data);

        // ✅ バリデーションエラーがないか確認
        if ($response->status() !== 302) {
            dd($response->getContent());
        }

        // ✅ 正しくリダイレクトされることを確認
        $response->assertStatus(302);
        $response->assertRedirect(route('item.sell.page'));

        // ✅ データベースに商品が保存されたか確認
        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => '商品名',
            'description' => '商品の説明',
            'price' => 1000,
            'condition' => '商品の状態',
            'category' => json_encode(['カテゴリ名']), // 配列は JSON で保存される可能性あり
        ]);

        // ✅ 画像が正しく保存されたか確認
        $savedItem = Item::first();
        Storage::disk('public')->assertExists('item_images/' . $savedItem->image);
    }
}
