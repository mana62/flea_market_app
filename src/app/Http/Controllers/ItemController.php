<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ItemRequest;

class ItemController extends Controller
{
    //商品一覧を表示
    public function index(Request $request)
    {
        //おすすめ(商品一覧)を取得して$tabに格納
        $tab = $request->query('tab', 'recommend');
        //検索部分のワードを取得して$inputに格納
        $input = $request->query('search', '');

        //タブごとのデータ取得(お気に入り)
        $items = ($tab === 'mylist' && Auth::check())
            ? Auth::user()->likedItems()->where('name', 'like', "%$input%")->get()
            : Item::where('name', 'like', "%$input%")->get();

        //自分の商品を除外
        if (Auth::check()) {
            $items = $items->where('user_id', '!=', Auth::id());
        }

        return view('item', compact('items', 'tab', 'input'));
    }

    //商品詳細ページ
    public function itemDetail($item_id)
    {
        //item_idを取得して$itemに格納
        $item = Item::findOrFail($item_id);
        //itemテーブルからコメントをプロフィールから取得して$commentsに格納
        $comments = $item->comments()->with('user.profile')->get();
        $categories = $item->categories;
        return view('item_detail', compact('item', 'comments', 'categories'));
    }

    //出品ページ
    public function sellItemPage()
    {
        //configからカテゴリーとコンディションを取得
        $categories = config('item.categories');
        $conditions = config('item.conditions');
        $item = new Item();
        //セッションから画像パスを取得
        $imagePath = session('item', 'image_path', null);
        return view('item_sell', compact('categories', 'conditions', 'imagePath'));
    }

    //商品の出品処理
    public function sellItem(ItemRequest $request)
    {
        //画像を保存してパスを取得
        $imagePath = $request->file('img')->store('item_images', 'public');

        //データベースに保存
        Item::create([
            'user_id' => Auth::id(),
            'name' => $request->input('name'),
            'brand' => $request->input('brandName'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'category' => $request->input('category'),
            'condition' => $request->input('condition'),
            'image' => basename($imagePath),
        ]);

        return redirect()->route('item.sell.page')->with('message', '商品を出品しました');
    }

    //画像アップロード
    public function uploadImage(Request $request, Item $item)
    {
        if ($request->hasFile('img')) {
            $request->validate(['img' => 'image|mimes:jpeg,png|max:2048']);
            $path = $request->file('img')->store('item_images', 'public');
            $item->update(['image' => basename($path)]);

            // セッションに画像パスを保存
            session(['image_path' => $path]);
        }

        return redirect()->route('item.sell.page')->with('message', '画像をアップロードしました');
    }

    //いいね機能
    public function toggleLike(Request $request, Item $item)
    {
        //ログインしているユーザーを取得
        $user = Auth::user();
        //ユーザーがいいねしてるアイテムに対して、指定されたアイテムIDをトグル（追加/削除）する
        $user->likedItems()->toggle($item->id);

        //json形式で返す
        return response()->json([
            //ユーザーが指定したアイテムをいいねしているかどうかを確認し、その結果をlikedキーに格納
            'liked' => $user->likedItems->contains($item->id),
            //いいねの数を数える
            'likesCount' => $item->likesCount(),
        ]);
    }

    //コメント投稿
    public function comment(CommentRequest $request, $itemId)
    {
        //データベースに保存
        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $itemId,
            'content' => $request->input('comment'),
        ]);

        return redirect()->route('item.detail', $itemId)->with('message', 'コメントを投稿しました');
    }
}
