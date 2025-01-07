<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;

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

    public function toggleLike(Request $request, Item $item)
    {
        $user = Auth::user();
        $user->likedItems()->toggle($item->id);
        $isLiked = $user->likedItems->contains($item->id);
        return response()->json(['liked' => $isLiked, 'likesCount' => $item->likesCount(),]);
    }


    public function comment(CommentRequest $request, $itemId)
    {
        $user = Auth::user();

        Comment::create([
            'user_id' => $user->id,
            'item_id' => $itemId,
            'content' => $request->input('comment')
        ]);
        return redirect()->route('item.detail', $itemId);
    }

}
