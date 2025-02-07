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
        $input = $request->input('search', '');
        $tab = $request->query('tab', 'recommend');

        $query = ($tab === 'mylist' && Auth::check())
            ? Auth::user()->likedItems()
            : Item::query();

        if ($input) {
            $query->where('name', 'like', "%$input%");
        }

        $items = $query->get();
        return view('item', compact('items', 'input', 'tab'));
    }
}
