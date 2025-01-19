<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    //商品検索
    public function searchItem(Request $request)
    {
        //検索キーワードを取得
        $input = $request->input('search', '');
        //タブ情報を取得
        $tab = $request->query('tab', 'recommend');

        //検索クエリを作成
        $query = ($tab === 'mylist' && Auth::check())
            ? Auth::user()->likedItems()
            : Item::query();

        if ($input) {
            $query->where('name', 'like', "%$input%");
        }

        //検索結果を取得
        $items = $query->get();

        return view('item', compact('items', 'input', 'tab'));
    }
}
