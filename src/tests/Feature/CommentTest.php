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

    //ログイン済みのユーザーはコメントを送信できる
    public function test_comment_only_user()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        Comment::factory()->create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント'
        ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'content' => 'テストコメント'
        ]);

        $response = $this->actingAs($user)->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertSee('テストコメント');
    }

    //ログイン前のユーザーはコメントを送信できない
    public function test_comment_not_user()
    {
        $item = Item::factory()->create();
        $commentContent = 'テストコメント';
        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertDontSee($commentContent);
    }

    //コメントが入力されていない場合、バリデーションメッセージが表示される
    public function test_validation_no_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route('item.comment', ['item_id' => $item->id]), [
            'content' => ''
        ]);
        $response->assertSessionHasErrors('content');
    }

    //コメントが255字以上の場合、バリデーションメッセージが表示される
    public function test_validation_over_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post(route('item.comment', ['item_id' => $item->id]), [
            'content' => str_repeat('あ', 256)
        ]);

        $response->assertSessionHasErrors('content');
    }
}