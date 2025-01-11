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
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $input = $request->query('search', '');
    
        if ($tab === 'mylist' && Auth::check()) {
            $items = Auth::user()->likedItems()->where('name', 'like', "%$input%")->get();
        } else {
            $items = Item::where('name', 'like', "%$input%")->get();
        }
    
        return view('item', compact('items', 'tab', 'input'));
    }

    public function itemDetail($item_id)
    {
        $item = Item::findOrFail($item_id);
        $comments = $item->comments()->with('user.profile')->get();
        return view('item_detail', compact('item', 'comments'));
    }

    public function sellItemPage(Request $request)
    {
        $categories = config('item.categories');
        $conditions = config('item.conditions');
        $item = new Item();
        return view('item_sell', compact('item', 'categories', 'conditions'));
    }

    public function sellItem(ItemRequest $request)
    {
        // 画像を保存し、パスを取得
        $imagePath = $request->file('img')->store('item_images', 'public');

        Item::create([
            'user_id' => Auth::id(),
            'name' => $request->input('name'),
            'brand' => $request->input('brand'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'category' => $request->input('category'),
            'condition' => $request->input('condition'),
            'image' => basename($imagePath),
        ]);

        return redirect()->route('item.sell.page')->with('message', '商品を出品しました');
    }

    public function uploadImage(Request $request, Item $item)
    {
        if ($request->hasFile('img')) {
            $request->validate(['img' => 'image|mimes:jpeg,png,jpg,gif|max:2048']);

            $originalName = $request->file('img')->getClientOriginalName();
            $fileName = now()->format('Ymd_His') . '_' . $originalName;
            $path = $request->file('img')->storeAs('public/images', $fileName);

            $item->image = basename($path);
            $item->save();
        }

        return redirect()->route('item.sell', $item->id)->with('message', '画像をアップロードしました');
    }

    public function toggleLike(Request $request, Item $item)
    {
        $user = Auth::user();
        $user->likedItems()->toggle($item->id);
        $isLiked = $user->likedItems->contains($item->id);
        return response()->json(['liked' => $isLiked, 'likesCount' => $item->likesCount()]);
    }

    public function comment(CommentRequest $request, $itemId)
    {
        $user = Auth::user();

        Comment::create([
            'user_id' => $user->id,
            'item_id' => $itemId,
            'content' => $request->input('comment'),
        ]);

        return redirect()->route('item.detail', $itemId)->with('message', 'コメントを投稿しました');
    }
}
