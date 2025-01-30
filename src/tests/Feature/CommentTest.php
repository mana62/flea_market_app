<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Comment;
use App\Models\User;
use App\Models\Item;

class CommentTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_comment_only_user()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
    

        // コメントを作成
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'item_id' => $item->id,
        'content' => 'テストコメント'
    ]);

    // コメントがDBに保存されていることを確認
    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'item_id' => $item->id,
        'content' => 'テストコメント'
    ]);
// 商品詳細ページを取得
$response = $this->actingAs($user)->get(route('item.detail', ['item_id' => $item->id]));

// Debug: HTML の内容を表示
dump($response->getContent());

// ページ内にコメントが含まれているか確認
$response->assertSee('テストコメント');
}
    
    public function test_comment_not_user()
    {
        $item = Item::factory()->create();
        $commentContent = 'テストコメント';
    
        // 未認証ユーザーがアクセスする
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
    
        // ステータスコードが200であることを確認
        $response->assertStatus(200);
    
        // コメントが表示されていないことを確認
        $response->assertDontSee($commentContent);
    }
    
    public function test_validation_no_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
    
        // コメント未入力で送信
        $response = $this->actingAs($user)->post(route('item.comment', ['item_id' => $item->id]), [
            'content' => ''
        ]);
    
        // バリデーションエラーの確認
        $response->assertSessionHasErrors('content');
    }
    
    public function test_validation_over_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
    
        // 256文字以上のコメントを送信
        $response = $this->actingAs($user)->post(route('item.comment', ['item_id' => $item->id]), [
            'content' => str_repeat('あ', 256)
        ]);
    
        // バリデーションエラーの確認
        $response->assertSessionHasErrors('content');
    }
}    