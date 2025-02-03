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
        // おすすめ(商品一覧)を取得して$tabに格納
        $tab = $request->query('tab', 'recommend');
        // 検索部分のワードを取得して$inputに格納
        $input = $request->query('search', '');
        // タブごとのデータ取得(お気に入り)
        if ($tab === 'mylist') {
            if (!Auth::check()) {
                // ゲストユーザーには「いいねした商品はありません」とメッセージを返す
                return view('item', [
                    'items' => collect(), // 空のコレクション
                    'tab' => $tab,
                    'input' => $input,
                    'message' => 'いいねした商品はありません',
                ]);
            }

            // ログインユーザーの場合は「いいねした商品」を取得
            $items = Auth::user()->likedItems()->where('name', 'like', "%$input%")->get();
        } else {
            // 通常のアイテム取得
            $items = Item::where('name', 'like', "%$input%")->get();
        }

        // 自分の商品を除外
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
        return view('item_sell', compact('categories', 'conditions'));
    }

    //商品の出品処理
    public function sellItem(ItemRequest $request)
    {
        $item = new Item();
 // ✅ カンマを削除した価格を取得
 $price = str_replace(',', '', $request->input('price'));

   // 画像のアップロード処理
   if ($request->filled('img_base64')) {
    $base64Image = $request->input('img_base64');
    if (strpos($base64Image, ',') !== false) {
        $imageData = explode(',', $base64Image)[1];

        // Base64デコード
        $imageDecoded = base64_decode($imageData);

            // ファイル名をユニークにする
            $timestamp = now()->format('Y-m-d_H-i-s');
            $originalFileName = uniqid();
            $extension = 'png';
            $imageName = "{$timestamp}_{$originalFileName}.{$extension}";

            // 画像を保存するディレクトリ
            $directory = storage_path("app/public/item_images/");
            if (!file_exists($directory)) {
                mkdir($directory, 0777, true); // フォルダがなければ作成
            }

            // 保存先のパス
            $imagePath = $directory . $imageName;

            // 画像を保存
            file_put_contents($imagePath, $imageDecoded);

            // Item モデルに画像を設定
            $item->image = $imageName;
            // **セッションに画像を保存（バリデーションエラー時に使う）**
            session(['itemImage' => $base64Image]);
        }
    } else {
        return redirect()->back()->withErrors(['img_base64' => '画像をアップロードしてください']);
    
    }
        // データベースに保存（画像のファイル名も含める）
        $item->fill([
            'user_id' => Auth::id(),
            'name' => $request->input('name'),
            'brand' => $request->input('brandName'),
            'price' => $price,
            'description' => $request->input('description'),
            'category' => $request->input('category'),
            'condition' => $request->input('condition'),
        ]);
    
        $item->save();

       // **出品成功時にセッションを削除**
    session()->forget('itemImage');

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

        // 最新の状態を取得してレスポンスを返す
        $isLiked = $user->likedItems()->where('item_id', $item->id)->exists();

        return response()->json([
            'liked' => $isLiked,
            'likesCount' => $item->likedBy()->count(),
        ]);
    }

    //コメント投稿
    public function comment(CommentRequest $request, $itemId)
    {
        //データベースに保存
        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $itemId,
            'content' => $request->input('content'),
        ]);

        return redirect()->route('item.detail', $itemId)->with('message', 'コメントを投稿しました');
    }
}