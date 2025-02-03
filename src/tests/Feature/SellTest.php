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

    // 商品出品画面にて必要な情報が保存できること（カテゴリ、商品の状態、商品名、商品の説明、販売価格）
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
        ]);

        // ✅ JSONカラムのデータを適切にチェック
        $this->assertTrue(
            Item::whereJsonContains('category', ['カテゴリ名'])->exists(),
            'カテゴリーが正しく保存されていません'
        );

        // ✅ 画像が正しく保存されたか確認
        $savedItem = Item::first();
        Storage::disk('public')->assertExists('item_images/' . $savedItem->img); // 'image' から 'img' に修正
    }
}
