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
    // 商品一覧を表示
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $input = $request->query('search', '');

        if ($tab === 'mylist') {
            if (!Auth::check()) {
                return view('item', [
                    'items' => collect(),
                    'tab' => $tab,
                    'input' => $input,
                    'message' => 'いいねした商品はありません',
                ]);
            }
            $items = Auth::user()->likedItems()->where('name', 'like', "%$input%")->get();
        } else {
            $items = Item::where('name', 'like', "%$input%")->get();
        }
        if (Auth::check()) {
            $items = $items->where('user_id', '!=', Auth::id());
        }

        return view('item', compact('items', 'tab', 'input'));
    }

    //商品詳細ページ
    public function itemDetail($item_id)
    {
        $item = Item::findOrFail($item_id);
        $comments = $item->comments()->with('user.profile')->get();
        $categories = $item->categories;

        return view('item_detail', compact('item', 'comments'));
    }

    //出品ページ
    public function sellItemPage()
    {
        $categories = config('item.categories');
        $conditions = config('item.conditions');

        return view('item_sell', compact('categories', 'conditions'));
    }

    //商品の出品処理
    public function sellItem(ItemRequest $request)
    {
        $item = new Item();
        $price = (float) $request->input('price');

        if ($request->filled('img_base64')) {
            $base64Image = $request->input('img_base64');
            if (strpos($base64Image, ',') !== false) {
                $imageData = explode(',', $base64Image)[1];
                $decodedImage = base64_decode($imageData);

                $timestamp = now()->format('Y-m-d_H-i-s');
                $originalFileName = uniqid();
                $extension = 'png';
                $imageName = "{$timestamp}_{$originalFileName}.{$extension}";

                $directory = storage_path("app/public/item_images/");
                if (!file_exists($directory)) {
                    mkdir($directory, 0777, true);
                }

                $imagePath = $directory . $imageName;
                file_put_contents($imagePath, $decodedImage);
                $item->image = $imageName;
                session(['itemImage' => $base64Image]);
            }
        } else {
            return redirect()->back()->withErrors(['img_base64' => '画像をアップロードしてください']);

        }
        $item->fill([
            'user_id' => Auth::id(),
            'name' => $request->input('name'),
            'price' => $price,
            'description' => $request->input('description'),
            'category' => $request->input('category'),
            'condition' => $request->input('condition'),
            'is_sold' => (int) false,
        ]);

        $item->save();
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
            session(['image_path' => $path]);
        }
        return redirect()->route('item.sell.page')->with('message', '画像をアップロードしました');
    }

    //いいね機能
    public function toggleLike(Request $request, Item $item)
    {
        $user = Auth::user();
        $user->likedItems()->toggle($item->id);
        $isLiked = $user->likedItems()->where('item_id', $item->id)->exists();

        return response()->json([
            'liked' => $isLiked,
            'likesCount' => $item->likedBy()->count(),
        ]);
    }

    //コメント投稿
    public function comment(CommentRequest $request, $itemId)
    {
        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $itemId,
            'content' => $request->input('content'),
        ]);
        return redirect()->route('item.detail', $itemId)->with('message', 'コメントを投稿しました');
    }
}